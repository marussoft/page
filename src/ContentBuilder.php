<?php

declare(strict_types=1);

namespace Marussia\Pages;

use Marussia\Pages\Content;

class ContentBuilder
{
    public function createContent(array $data) : Content
    {
        return new Content($data);
    }
}
