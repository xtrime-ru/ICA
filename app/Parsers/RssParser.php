<?php


namespace App\Parsers;


use App\Models\Post;
use Symfony\Component\DomCrawler\Crawler;

class RssParser extends HtmlParser
{
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

    protected function prepareSource(string $body, array $headers): string
    {
        //Remove zero width caracters, to properly use  symfony dom crawler xml detection
        $body = preg_replace('/^[\x{200B}-\x{200D}\x{FEFF}]/u', '', $body);

        return parent::prepareSource($body, $headers);
    }

    public function getPostUrl(Crawler $element): ?string
    {
        return $element->filter($this->source->parser_rules->urlPath)->first()->text();
    }

    protected function parsePost(Crawler $element): ?Post
    {
        $post = parent::parsePost($element);
        $imageElement = $element->filter($this->source->parser_rules->imgPath);
        if ($imageElement->count() && !$post->image) {
            $post->image = $imageElement->first()->attr('url') ?? $imageElement->first()->text() ?? null;
        }
        return $post;
    }
}
