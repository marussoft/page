<?php

declare(strict_types=1);

namespace Marussia\Content\Entities;

class Page
{
    public $id;

    public $name;

    public $title;

    public $slug;
    
    public $isActive;

    public $options = [];
}
