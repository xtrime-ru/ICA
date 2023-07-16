<?php


namespace App\Parsers;


use Symfony\Component\DomCrawler\Crawler;

class RssParser extends HtmlParser
{

    public const TYPE = 'rss';

    protected function getCharset(string $body, array $headers): ?string
    {
        if (strlen(preg_match('/encoding=["]{0,1}([-\w]+)["]{0,1}/', $body, $matches)) > 0) {
            $result = $matches[1] ?? null;
        } else {
            $result = null;
        }

        if (strlen($result) < 1) {
            $result = "utf-8";
        }

        return strtolower($result);
    }

    public function getPostUrl(Crawler $element): ?string
    {
        return $element->filter($this->source->parser_rules->urlPath)->first()->text();
    }
}
