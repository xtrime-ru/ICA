<?php


namespace App\Parsers;


use QL\Dom\Elements;

class RssParser extends HtmlParser
{

    public const TYPE = 'rss';

    protected function getCharset(string $body): ?string
    {
        if (strlen(preg_match('/encoding=["]{0,1}([-\w]+)["]{0,1}/', $body, $matches)) > 0) {
            $result = $matches[1] ?? null;
        } else {
            $result = null;
        }

        return $result;
    }

    protected function convertEncoding(string $body): string
    {
        $charset = $this->getCharset($body);
        if (strlen($charset) < 1) {
            $charset = "utf-8";
        }

        $body = mb_convert_encoding($body, "UTF-8", $charset);
        $body = str_replace('encoding="' . $charset . '"', 'encoding="utf-8"', $body);
        $body = str_replace('encoding=' . $charset, 'encoding=utf-8', $body);

        $body = preg_replace('/(<[\/]{0,1})link([\s\S]*?>)/u', "$1link_corrected$2", $body);
        $body = preg_replace('/\<!\[CDATA\[([\s\S]+?)\]\]>/u', '$1', $body);

        $body = htmlspecialchars_decode($body);
        return $body;
    }

    public function getPostUrl(Elements $element): ?string
    {
        return $element->find($this->rules->urlPath)->eq(0)->text();
    }
}
