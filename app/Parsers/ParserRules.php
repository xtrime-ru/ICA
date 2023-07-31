<?php


namespace App\Parsers;


use App\Enums\SourceTypes;
use Nette\Utils\Json;

class ParserRules
{
    public string $posts = '';
    public string $imgPath = '';
    public string $headerPath = '';
    public string $textPath = '';
    public string $urlPath = '';
    private SourceTypes $type;

    public function __construct(SourceTypes $type, ?string $rules)
    {
        $this->type = $type;
        if ($this->type === SourceTypes::RSS || $this->type === SourceTypes::TELEGRAM) {
            $this->posts = 'item, entry';
            $this->imgPath = 'enclosure, img:first-of-type, video';
            $this->headerPath = 'title';
            $this->textPath = "description, content";
            $this->urlPath = "guid:not([ispermalink*='false']):contains('//'), link";
        } else {
            if ($rules === null) {
                return;
            }
            $rules = json_decode($rules, true, 10,JSON_THROW_ON_ERROR);
            foreach ($rules as $name => $rule) {
                if (property_exists($this, $name)) {
                    $this->{$name} = $rule;
                } else {
                    throw new \InvalidArgumentException("Unknown property $name");
                }
            }
        }
    }

    public function __toString(): string {
        if ($this->type === SourceTypes::RSS) {
            return '';
        }
        return Json::encode(get_object_vars($this), JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);
    }
}
