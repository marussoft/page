<?php

declare(strict_types=1);

namespace Marussia\Content\Exceptions;

class CreatePageActionException extends \Exception
{
    public function __construct(\Throwable $exception)
    {
        $error = 'Create page error: ' . $exception->getMessage() . ' trace: ' . $exception->getTraceAsString() . ' in line: ' . $exception->getLine();
    }
}
