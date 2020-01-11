<?php

declare(strict_types=1);

namespace Marussia\Pages\TableBuilders;

trait NameBuilderTrait
{
    protected function makeValuesTableName(string $pageName) : string
    {
        return strtolower('page_' . $pageName . '_fields_values');
    }
}
