<?php


namespace App\Parsers;


use Nette\Utils\Json;

class ParserRules
{
    public string $posts = '';
    public string $imgPath = '';
    public string $headerPath = '';
    public string $textPath = '';
    public string $urlPath = '';

    public function __construct(?array $rules)
    {
        foreach ((array)$rules as $name => $rule) {
            if (property_exists($this, $name)) {
                $this->{$name} = $rule;
            }
        }
    }

    public function __toString() {
        return Json::encode(get_object_vars($this), JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);
    }
}
