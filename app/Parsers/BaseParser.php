<?php


namespace App\Parsers;


use App\Models\Post;
use App\Models\Source;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

abstract class BaseParser implements ParserInterface
{
    protected Source $source;
    protected Client $client;

    public function __construct(Source $source, Client $client)
    {
        $this->source = $source;
        $this->client = $client;

    }

    public function run(): void
    {
        Log::info('Start parsing url: ' . $this->source->parser_url);

        $url = $this->getUrl();
        [$body, $headers] = $this->loadSource($url);
        $body = $this->prepareSource($body, $headers);
        $posts = $this->findPosts($body);
        $this->savePosts($posts);
    }

    /**
     * @return array{0: string, 1:array<string,string[]>}
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function loadSource(string $url): array
    {
        $response = $this->client->get($url);
        $body = (string) $response->getBody();
        $headers = $response->getHeaders();

        if ($response->getStatusCode() !== 200) {
            throw new \Exception('Wrong response status');
        }
        return [$body, $headers];
    }

    protected abstract function prepareSource(string $body, array $headers): string;

    /**
     * @return Post[]
     */
    protected abstract function findPosts(string $body): array;

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

    protected function getUrl(): string
    {
        return $this->source->parser_url;
    }

    public static function cropLink(string $url, int $crop_length = 255): string {

        $urlParsed = parse_url($url);
        $urlParsed['scheme'] = $urlParsed['scheme']?:'http';
        if (!empty($urlParsed['path'])){
            $urlParsed['path'] = urldecode($urlParsed['path']);
        }


        if (!empty($urlParsed['host'])){
            $result = $urlParsed['scheme'] .'://'.$urlParsed['host'] ;

            if (!empty($urlParsed['path'])){
                $result .= $urlParsed['path'];
            }
            if (!empty($urlParsed['query'])){
                $result .= '?'.$urlParsed['query'];
            }
            if (!empty($urlParsed['fragment'])){
                $result .= '#'.$urlParsed['fragment'];
            }

        }

        $delimeters = array(
            '&utm_source=',
            '?utm_source=',
            'utm_source='
        );

        $delimeters_optional = array(
            '#',
            '&',
            '?'
        );

        foreach($delimeters as $delim){
            while(mb_strpos($result,$delim) !== false) {
                $url_arr = explode($delim,$result);
                array_pop($url_arr);
                $result = implode($delim,$url_arr);
            }
        }



        foreach($delimeters_optional as $delim){
            while(mb_strlen($result)>$crop_length && mb_strpos($result,$delim)!==false){
                $url_arr = explode($delim,$result);
                array_pop($url_arr);
                $result = implode($delim,$url_arr);
            }
        }

        return $result;
    }
}
