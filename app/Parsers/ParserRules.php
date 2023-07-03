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
    private string $type;

    public function __construct(string $type, ?string $rules)
    {
        $this->type = $type;
        if ($this->type === RssParser::TYPE) {
            $this->posts = 'item, entry';
            $this->imgPath = 'enclosure, img:eq(0), video';
            $this->headerPath = 'title';
            $this->textPath = "description, content";
            $this->urlPath = "guid:not([ispermalink*='false']):contains('//'), link_corrected";
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
        if ($this->type === RssParser::TYPE) {
            return '';
        }
        return Json::encode(get_object_vars($this), JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);
    }
}
