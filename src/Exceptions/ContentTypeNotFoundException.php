<?php

declare(strict_types=1);

namespace Marussia\Content\Exceptions;

class ContentTypeNotFoundException extends \Exception
{
    public function __construct(string $contentTypeName)
    {
        parent::__construct('Content type "' . $contentTypeName . '" not found.');
    }
}
