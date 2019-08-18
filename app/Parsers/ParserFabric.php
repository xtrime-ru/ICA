<?php


namespace App\Parsers;


use App\Source;
use Doctrine\DBAL\Exception\DatabaseObjectNotFoundException;

class ParserFabric
{
    /**
     * @param int $sourceId
     *
     * @return ParserInterface
     * @throws \Exception
     */
    public static function get(int $sourceId): ParserInterface
    {
        /** @var Source $source */
        $source = Source::where('id', $sourceId)->first();

        if (!$source || !$source->exists) {
            throw new \Exception('Not found source id: ' . $sourceId);
        }

        switch ($source->parser_type) {
            case 'rss':
                $parser = new RssParser($source);
                break;
            case 'html':
                $parser = new HtmlParser($source);
                break;
            default:
                throw new \UnexpectedValueException('Not implemented parser type');
        }

        return $parser;
    }
}
