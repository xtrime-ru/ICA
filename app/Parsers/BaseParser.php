<?php


namespace App\Parsers;


use App\Models\Source;

class BaseParser  implements ParserInterface
{
    /** @var Source */
    protected $source;
    protected $url;

    public function __construct(Source $source)
    {
        $this->source = $source;
        $this->url = $source->parser_url;
    }

    public function run() {}
}
