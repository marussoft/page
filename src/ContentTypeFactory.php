<?php

declare(strict_types=1);

namespace Marussia\Content;

use Marussia\Content\Entities\ContentType;

class ContentTypeFactory
{
    public function createFromArray(array $data) : ContentType
    {
        $contentType = new ContentType;

        $contentType->id = $data['id'];
        $contentType->name = $data['name'];
        $contentType->title = $data['title'];
        if (array_key_exists('description', $data) && mb_strlen($data['description']) > 0){
            $contentType->description = $data['description'];
        }

        if (array_key_exists('options', $data) && count() > 0) {
            $contentType->options = $data['options'];
        }
        return $contentType;
    }
}
