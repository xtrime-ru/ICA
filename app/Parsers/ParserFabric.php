<?php


namespace App\Parsers;


use App\Enums\SourceTypes;
use App\Models\Source;
use Doctrine\DBAL\Exception\DatabaseObjectNotFoundException;
use GuzzleHttp\Client;

class ParserFabric
{
    /**
     *
     * @return ParserInterface
     * @throws \Exception
     */
    public static function get(Source $source, float $timeout = 3.0): ParserInterface
    {
        $parser = match ($source->type) {
            SourceTypes::RSS => new RssParser($source, new Client(['timeout' => $timeout])),
            SourceTypes::HTML => new HtmlParser($source, new Client(['timeout' => $timeout])),
            SourceTypes::VK => new VkParser($source, new Client(['timeout' => $timeout])),
            SourceTypes::TELEGRAM => new TelegramParser($source, new Client(['timeout' => $timeout])),
            default => throw new \UnexpectedValueException('Not implemented parser type: ' . $source->type->name),
        };

        return $parser;
    }
}
