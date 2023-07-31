<?php


namespace App\Parsers;


use App\Models\Post;

interface ParserInterface
{
    /**
     * @return Post[]
     */
    public function getPosts(): array;
}
