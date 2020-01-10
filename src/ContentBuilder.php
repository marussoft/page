<?php

declare(strict_types=1);

namespace Marussia\Content;

use Marussia\Content\Content;

class ContentBuilder
{
    public function createContent(array $data) : Content
    {
        return new Content($data);
    }
}
