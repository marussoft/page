<?php

declare(strict_types=1);

namespace Marussia\Content\TableBuilders;

use Marussia\Content\Exceptions\UnknownColumnDataTypeException;

trait DataTypeBuilderTrait
{
    protected function prepareType(array $dataType) : string
    {
        switch (key($dataType)) {
            case 'integer':
                $type = $this->createIntegerType(current($dataType));
                break;
            case 'character':
                $type = $this->createCharacterType(current($dataType));
                break;
            case 'boolean':
                $type = $this->createBooleanType(current($dataType));
                break;
            default:
                throw new UnknownColumnDataTypeException(key($dataType));
        }
        return $type;
    }

    private function createCharacterType(int $size) : string
    {
        if ($size === 0) {
            return 'TEXT';
        }
        return 'VARCHAR(' . strval($size) . ')';
    }

    private function createBooleanType(bool $value) : string
    {
        $default = $value ? 'TRUE' : 'FALSE';
        return 'BOOLEAN DEFAULT ' . $default;
    }

    private function createIntegerType(int $size) : string
    {
        return $size <= 5 ? 'SMALLINT' : 'INTEGER';
    }
}
