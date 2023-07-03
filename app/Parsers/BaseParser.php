<?php


namespace App\Parsers;


use App\Models\Source;

abstract class BaseParser  implements ParserInterface
{
    protected Source $source;
    protected string $url;

    public function __construct(Source $source)
    {
        $this->source = $source;
        $this->url = $source->parser_url;
    }

    public abstract function run();
}
