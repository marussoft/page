<?php

declare(strict_types=1);

namespace Marussia\Pages\Actions\Bundles;

use Marussia\ContentField\Actions\FillAction;
use Marussia\ContentField\FieldDataFactory;
use Marussia\ContentField\Field;
use Marussia\ContentField\FieldData;
use Marussia\ContentField\FieldFactory;

class FillFieldProvider
{
    protected $fillFieldAction;

    protected $fieldDataFactory;

    protected $fieldFactory;

    public function __construct(FillAction $fillFieldAction, FieldDataFactory $fieldDataFactory, FieldFactory $fieldFactory)
    {
        $this->fillFieldAction = $fillFieldAction;
        $this->fieldDataFactory = $fieldDataFactory;
        $this->fieldFactory = $fieldFactory;
    }

    public function createFieldData(array $data) : FieldData
    {
        return $this->fieldDataFactory->create($data);
    }

    public function fillField(FieldData $fieldData) : Field
    {
        return $this->fillFieldAction->execute($fieldData);
    }

    public function createFieldWithoutHandler(string $name, $value) : Field
    {
        return $this->fieldFactory->create(['name' => $name, 'value' => $value]);
    }
}
