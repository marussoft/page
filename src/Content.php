<?php

declare(strict_types=1);

namespace Marussia\Content;

class Content
{
    public function __construct(array $data)
    {
        foreach ($data as $key => $value) {
            $this->{$key} = $value;
        }
    }

    public function isValid()
    {
        foreach ($this as $field) {
            if (count($field->errors) > 0) {
                return false;
            }
        }

        return true;
    }
}
