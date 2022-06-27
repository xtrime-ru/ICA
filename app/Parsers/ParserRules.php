<?php


namespace App\Parsers;


use Nette\Utils\Json;

class ParserRules
{
    public $posts = '';
    public $imgPath = '';
    public $headerPath = '';
    public $textPath = '';
    public $urlPath = '';

    public function __construct(?array $rules)
    {
        foreach ($rules as $name => $rule) {
            if (property_exists($this, $name)) {
                $this->{$name} = $rule;
            }
        }
    }

    public function __toString() {
        return Json::encode(get_object_vars($this), JSON_PRETTY_PRINT);
    }
}
