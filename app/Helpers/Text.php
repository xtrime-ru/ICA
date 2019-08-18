<?php


namespace App\Helpers;


class Text
{
    public static function htmlToText($text){
        $text = htmlspecialchars_decode(htmlspecialchars_decode($text));

        $text = strip_tags($text, '<br>');

        $breaks = ['<br />', '<br>', '<br/>'];
        $text = trim(str_ireplace($breaks, PHP_EOL, $text));
        $text = preg_replace('/([\s]{2})[\s]+/u', '$1', $text);
        $text = preg_replace('~http[s]?://\S*~u', '', $text);

        return $text;
    }

    public static function cropText(string $text, int $length, string $tail = ' [...]')
    {
        $trimLength = $length - mb_strlen($tail);
        return mb_strimwidth($text, 0, $trimLength, $tail);
    }
}
