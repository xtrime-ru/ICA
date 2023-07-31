<?php


namespace App\Parsers;

use App\Models\Post;
use Illuminate\Support\Facades\Log;
use Symfony\Component\DomCrawler\Crawler;

class HtmlParser extends BaseParser
{
    protected function getPostUrl(Crawler $element): ?string
    {
        try {
            return $element->filter($this->source->parser_rules->urlPath)->first()->attr('href');
        } catch (\Throwable $e) {
            Log::error($e);
            return null;
        }

    }

    protected function prepareSource(string $body, array $headers): string
    {
        $encoding = $this->getCharset($body, $headers);
        $body = $this->convertEncoding($body, $encoding);
        $body = $this->deleteScripts($body);
        $body = $this->fixSrc($body);

        return $body;
    }

    protected function convertEncoding(string $body, ?string $encoding): string
    {
        if (!$encoding || strtoupper($encoding) !== 'UTF-8') {
            $body = mb_convert_encoding($body, 'UTF-8', $encoding);
        }
        $body = preg_replace('/(charset *= *(["\']?))([a-zA-Z\-0-9_:.]+)/i', "$1UTF-8$2", $body);
        return $body;
    }

    protected function getCharset(string $body, array $headers): ?string
    {
        preg_match('/charset=(["\']?)([-\w]+)\1/', $headers['Content-Type'][0] ?? '', $matches);
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
        $parsedUrl = parse_url($this->source->parser_url);
        if (empty($parsedUrl['scheme'])) {
            return $body;
        }
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
     * @return Post[]
     */
    public function getPosts(): array
    {
        $dom = new Crawler($this->body, $this->source->parser_url);

        $posts = $dom->filter($this->source->parser_rules->posts);
        $result = [];
        $posts->each(function($post) use(&$result) {
            $item = $this->parsePost($post);
            if ($item) {
                $result[mb_strtolower($item->url)] = $item;
            }
        });

        return $result;
    }

    protected function parsePost(Crawler $element): ?Post
    {
        $url = $this->getPostUrl($element);
        if (!$url) {
            Log::notice('No url in post', [
                'element' => $element->outerHtml(),
            ]);
            return null;
        }
        /** @var Post $post */
        $post = Post::firstOrNew(['url' => $url]);

        if ($this->source->parser_rules->headerPath) {
            $post->title = $element->filter($this->source->parser_rules->headerPath)->first()->text();
        }
        if ($this->source->parser_rules->textPath) {
            $post->description = $element->filter($this->source->parser_rules->textPath)->first()->text();
        }

        if ($this->source->parser_rules->imgPath) {
            $imageElement = $element->filter($this->source->parser_rules->imgPath);
            if ($imageElement->count()) {
                $post->image = $imageElement->first()->attr('scr');
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

            foreach ([
                $imageElement->count() ? $imageElement->outerHtml() : '',
                $element->html()
            ] as $item) {
                if (!$post->image) {
                    preg_match(
                        '/(?:<|&lt;)img[\s\S]*?src=(["\'])([\S]*\/\/[\S]*?[^\\\])\1/ui',
                        $item,
                        $matches
                    );
                    $post->image = $matches[2] ?? '';
                }
            }

            $post->image = trim($post->image) ?: null;



        }

        return $post;
    }
}
