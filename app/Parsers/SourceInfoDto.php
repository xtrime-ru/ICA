<?php

namespace App\Parsers;

class SourceInfoDto
{
    public function __construct(
        public string $type,
        public string $url,
        public string $icon,
        public string $title,
    ) {}

}