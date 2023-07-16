<?php


namespace App\Parsers;


use App\Models\Source;
use Doctrine\DBAL\Exception\DatabaseObjectNotFoundException;
use GuzzleHttp\Client;

class ParserFabric
{
    /**
     * @param int $sourceId
     *
     * @return ParserInterface
     * @throws \Exception
     */
    public static function get(int $sourceId, float $timeout = 3.0): ParserInterface
    {
        /** @var Source $source */
        $source = Source::where('id', $sourceId)->first();

        if (!$source || !$source->exists) {
            throw new \Exception('Not found source id: ' . $sourceId);
        }

        switch ($source->parser_type) {
            case 'rss':
                $parser = new RssParser($source, new Client(['timeout'=> $timeout]));
                break;
            case 'html':
                $parser = new HtmlParser($source, new Client(['timeout'=> $timeout]));
                break;
            case 'social':
                switch ($source->social) {
                    case 'vk':
                        $parser = new VkParser($source, new Client(['timeout'=> $timeout]));
                        break;
                    default:
                        throw new \UnexpectedValueException('Not implemented parser social: ' . $source->parser_type);
                }

                break;
            default:
                throw new \UnexpectedValueException('Not implemented parser type: ' . $source->parser_type);
        }

        return $parser;
    }
}
