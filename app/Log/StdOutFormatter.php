<?php


namespace App\Log;


use Monolog\Formatter\LineFormatter;
use Monolog\Utils;

class StdOutFormatter extends LineFormatter
{
    public const SIMPLE_FORMAT = "[%datetime%] %channel%.%level_name%: %message%\n%context%\n%extra%\n";

    public function __construct(
        $format = null,
        $dateFormat = null,
        $allowInlineLineBreaks = true,
        $ignoreEmptyContextAndExtra = true
    ) {
        parent::__construct($format, $dateFormat, $allowInlineLineBreaks, $ignoreEmptyContextAndExtra);
    }

    public function format(array $record): string
    {
        $vars = $this->normalize($record);

        $output = $this->format;

        if ($this->ignoreEmptyContextAndExtra) {
            if (empty($vars['context'])) {
                unset($vars['context']);
                $output = preg_replace('~\s%context%~', '', $output);
            }

            if (empty($vars['extra'])) {
                unset($vars['extra']);
                $output = preg_replace('~\s%extra%~', '', $output);
            }
        }

        foreach ($vars as $var => $val) {
            if (false !== strpos($output, '%'.$var.'%')) {
                $output = str_replace('%'.$var.'%', $this->stringify($val), $output);
            }
        }

        // remove leftover %extra.xxx% and %context.xxx% if any
        if (false !== strpos($output, '%')) {
            $output = preg_replace('/%(?:extra|context)\..+?%/', '', $output);
        }

        return $output;

    }


    protected function toJson($data, bool $ignoreErrors = true): string
    {
        $options = JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES;
        if ($ignoreErrors) {
            $options |= JSON_PARTIAL_OUTPUT_ON_ERROR | JSON_INVALID_UTF8_IGNORE;
        }

        return Utils::jsonEncode($data, $options);
    }
}
