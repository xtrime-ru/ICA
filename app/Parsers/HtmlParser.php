<?php


namespace App\Parsers;

use App\Models\Post;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use QL\Dom\Elements;
use QL\QueryList;

class HtmlParser extends BaseParser
{
    /** @var ParserRules */
    private $rules;

    private $headers = [];

    public function run()
    {
        Log::info('Start parsing url: ' . $this->url);

        $this->rules = $this->source->parser_rules;

        $body = $this->loadSource();
        $dom = $this->prepareDom($body);
        $posts = $this->findPosts($dom);
        $this->savePosts($posts);

    }

    private function loadSource(): string
    {
        $client = new Client(['timeout'=>3.0]);
        $response = $client->get($this->url);
        $body = (string) $response->getBody();
        $this->headers = $response->getHeaders();

        if ($response->getStatusCode() !== 200) {
            throw new \Exception('Wrong response status');
        }
        return $body;
    }

    private function prepareDom($body): QueryList
    {
        $body = $this->convertEncoding($body);
        $body = $this->deleteScripts($body);
        $body = $this->fixSrc($body);

        return  QueryList::getInstance()->setHtml($body);

    }

    private function convertEncoding(string $body): string
    {
        $body = mb_convert_encoding($body, 'UTF-8', $this->getCharset($body));
        return $body;
    }

    private function getCharset(string $body)
    {
        preg_match('/charset=(["\']?)([-\w]+)\1/', $this->headers['Content-Type'][0] ?? '', $matches);
        if (empty($matches[2])) {
            preg_match('/charset=(["\']?)([-\w]+)\1/', $body, $matches);
        }
        return $matches[2] ?? null;
    }

    private function deleteScripts(string $body): string
    {
        $body = preg_replace('~<script[\s\S]*?</script>~ui', '', $body);

        $list = '(?:onmouseout|onmouseover|onmousedown|onclick|onload|onresize)';
        $body = preg_replace("/ ?{$list} ?= ?(['\"])[\s\S]*?(?:[^\\\]\1)/ui", '', $body);

        return $body;
    }

    private function fixSrc(string $body): string
    {
        $parsedUrl = parse_url($this->url);
        $scheme = ($parsedUrl['scheme'] ?: 'http') . '://';
        $host = $scheme . $parsedUrl['host'].'/';

        $body = preg_replace('/src=[\'"] ?([\s\S]*?)[\'"]/u', 'src="$1"', $body);
        $body = preg_replace('~src="[\s]?//~u', "src=\"{$scheme}", $body);
        $body = preg_replace('/src="([?#])/u', "src=\"{$host}\$1", $body);

        $body = preg_replace('/href=[\'"] ?([\s\S]*?)[\'"]/u', 'href="$1"', $body);
        $body = preg_replace('~href="[\s]?//~u', "href=\"{$scheme}", $body);
        $body = preg_replace('/href="([\?\#])/u', "href=\"{$host}\$1", $body);

        $body = preg_replace('/url=[\'"] ?([\s\S]*?)[\'"]/u', 'url="$1"', $body);
        $body = preg_replace('~url="//~u', 'url="' . $scheme, $body);
        $body = preg_replace('/url="([\?\#])/u', "url=\"{$host}\$1", $body);

        $body = preg_replace('~src="/~u', "src=\"{$host}", $body);
        $body = preg_replace('~href="/~u', "href=\"{$host}", $body);

        $body = preg_replace(
            '~background-image: ?url ?\( ?//~u',
            "background-image: url({$scheme}",
            $body
        );
        $body = preg_replace('/%25/u', '%', $body);
        $body = preg_replace('/&amp;/u', '&', $body);
        $body = preg_replace(
            '~background: ?url ?\( ?/~u',
            "background: url({$host}",
            $body
        );
        $body = preg_replace(
            '~background-image: ?url ?\( ?/~u',
            "background-image: url({$host}",
            $body
        );

        $body = preg_replace('~src ?= ?" ?http[s]?://~u', "src=\"{$scheme}", $body);

        return $body;
    }

    /**
     * @param QueryList $dom
     *
     * @return Post[]
     */
    private function findPosts(QueryList $dom): array
    {
        $posts = $dom->find($this->rules->posts);
        $result = [];
        $posts->map(function($post) use(&$result) {
            $item = $this->parsePost($post);
            if ($item) {
                $result[] = $item;
            }
        });
        $dom->destruct();

        return $result;
    }

    private function parsePost(Elements $element): ?Post
    {
        $url = $element->find($this->rules->urlPath)->eq(0)->attr('href');
        if (!$url) {
            return null;
        }
        $post = Post::firstOrNew(['url' => $url]);

        if ($this->rules->headerPath) {
            $post->title = $element->find($this->rules->headerPath)->eq(0)->text();
        }
        if ($this->rules->textPath) {
            $post->description = $element->find($this->rules->textPath)->eq(0)->text();
        }

        if ($this->rules->imgPath && $element->find($this->rules->imgPath)->count()) {
            $imageElement = $element->find($this->rules->imgPath)->eq(0);
            $post->image = $imageElement->attr('scr');

            if (!$post->image) {
                preg_match(
                    '/<img[\s\S]*?src=(["\'])([\S]*\/\/[\S]*?[^\\\])\1/ui',
                    $imageElement->parent()->html(),
                    $matches
                );
                $post->image = $matches[2] ?? '';
                $post->image = trim($post->image);
            }
            if (!$post->image) {
                preg_match(
                    '/background-image:[ ]*url\((["\'])*([\s\S]*[^\\\])\1*\)/ui',
                    $imageElement->attr('style'),
                    $matches
                );
                $post->image = $matches[2] ?? '';
                $post->image = trim($post->image);
            }
        }

        return $post;
    }

    /**
     * @param Post[] $posts
     */
    protected function savePosts(array $posts): void
    {
        $created = 0;
        $updated = 0;
        foreach ($posts as $post) {
            if (!$post instanceof Post) {
                Log::error('post', ['post' => $post]);
                throw new \UnexpectedValueException('Wrong type of post');
            }
            Log::debug('post', ['post' => $post->toArray()]);
            if (!$post->exists) {
                $created++;
            } else {
                $updated++;
            }
            $post->save();
            $post->sources()->syncWithoutDetaching([$this->source->id]);
        }

        Log::info('Saved: ', [
            'source_id'=>$this->source->id,
            'source_url' => $this->source->parser_url,
            'created'=>$created,
            'updated'=>$updated,
            'total' => count($posts)
        ]);
    }

}
