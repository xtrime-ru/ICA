<?php


namespace App\Parsers;


use App\Models\Post;
use App\Models\Source;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Illuminate\Support\Facades\Log;

abstract class BaseParser implements ParserInterface
{
    protected Source $source;
    protected Client $client;

    protected string $body;
    /**
     * @var array<string,string[]>
     */
    private array $headers;

    public function __construct(Source $source, Client $client)
    {
        $this->source = $source;
        $this->client = $client;

        Log::info('start parsing source', [
            'source' => $source->toArray(),
        ]);
        $url = $this->getUrl();
        [$this->body, $this->headers] = $this->loadSource($url);
        $this->body = $this->prepareSource($this->body, $this->headers);
    }



    /**
     * @return array{0: string, 1:array<string,string[]>}
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function loadSource(string $url): array
    {

        $response = $this->client->get($url, [
            RequestOptions::IDN_CONVERSION => true,
        ]);
        $body = (string) $response->getBody();
        $headers = $response->getHeaders();

        if ($response->getStatusCode() !== 200) {
            throw new \Exception('Wrong response status');
        }
        return [$body, $headers];
    }

    protected abstract function prepareSource(string $body, array $headers): string;


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
