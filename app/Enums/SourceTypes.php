<?php

namespace App\Enums;

enum SourceTypes: string
{
    case RSS = 'rss';
    case HTML = 'html';
    case VK = 'vk';
    case TELEGRAM = 'telegram';
}
