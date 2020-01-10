<?php

declare(strict_types=1);

namespace Marussia\Page\Actions;

use Marussia\Page\TableBuilders\PageBuilder;
use Marussia\ContentField\Actions\GetFieldDataTypeAction;

class CreatePageFieldValue
{
    private $builder;

    private $getDataType;

    private $dataType = [];

    private $pageName = '';

    private $fieldName = '';

    private $fieldType = '';

    public function __construct(PageBuilder $builder, GetFieldDataTypeAction $getDataType)
    {
        $this->builder = $builder;
        $this->getDataType = $getDataType;
    }

    public function execute()
    {
        if (strlen($this->pageName) === 0) {
            throw new PageNameNotSetException;
        }

        if (strlen($this->fieldName) === 0) {
            throw new FieldNameNotSetException;
        }

        if (strlen($this->fieldType) === 0) {
            throw new FieldTypeNotSetException;
        }

        $dataType = count($this->dataType) === 0 ? $this->getDataType->execute($this->fieldType) : $this->dataType;

        try {
            $this->builder->beginTransaction();
            $this->builder->createFieldValue($this->pageName, $this->fieldName, $dataType);
            $this->builder->commit();
        } catch (\Throwable $exception) {
            $this->builder->rollBack();
            throw $exception;
        }
    }

    public function boolean(bool $value = true) : self
    {
        $this->dataType['boolean'] = $value;
        return $this;
    }

    public function character(int $size = 0) : self
    {
        $this->dataType['character'] = $size;
        return $this;
    }

    public function integer(int $size) : self
    {
        $this->dataType['integer'] = $size;
        return $this;
    }

    public function pageName(string $pageName) : self
    {
        $this->pageName = strtolower($pageName);
        return $this;
    }

    public function fieldName(string $fieldName) : self
    {
        $this->fieldName = strtolower($fieldName);
        return $this;
    }

    public function fieldType(string $fieldType) : self
    {
        $this->fieldType = strtolower($fieldType);
        return $this;
    }
}
