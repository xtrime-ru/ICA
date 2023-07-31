<?php


namespace App\Parsers;


class TelegramParser extends RssParser
{

    protected function getUrl(): string
    {
        $telegramRssUrl = getenv('TELEGRAM_RSS_URL');
        return $telegramRssUrl . $this->source->parser_url;
    }

}
