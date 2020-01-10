<?php

declare(strict_types=1);

namespace Marussia\Content\Actions;

use Marussia\Contracts\ActionInterface;
use Marussia\Content\Repositories\PageRepository;
use Marussia\ContentField\Actions\GetFieldWithoutHandler;
use Marussia\ContentField\Actions\CreateFieldDataAction;
use Marussia\Content\Content;
use Marussia\Content\ContentBuilder;

class TranslatePageAction extends AbstractAction implements ActionInterface
{
    private $repository;

    private $getFieldWithoutHandler;

    private $createFieldData;

    private $contentBuilder;

    private $page;

    private $translation;

    public function __construct(PageRepository $repository, CreateFieldDataAction $createFieldData, GetFieldWithoutHandler $getFieldWithoutHandler, ContentBuilder $contentBuilder)
    {
        $this->repository = $repository;
        $this->createFieldData = $createFieldData;
        $this->getFieldWithoutHandler = $getFieldWithoutHandler;
        $this->contentBuilder = $contentBuilder;
    }

    public function execute() : Content
    {
        if ($this->translation === null) {
            throw new TranslationNotReceiptException;
        }

        if ($this->page === null) {
            throw new PageForTranslationNotReceiptException;
        }

        $fields = $this->repository->getFields($this->page->id);

        $contentValues = [];

        foreach ($this->translation as $fieldName => $translate) {
            if (property_exists($this->page, $fieldName) === false) {
                throw new UnknownFieldFoTranslationException($fieldName);
                continue;
            }

            if ($fields->has($fieldName)) {
                $fieldData = $this->createFieldData->data($fields->get($fieldName))->execute();
                $fieldData->value = $updateData;
                $contentValues[$fieldName] = $this->createFieldInput->fieldData($fieldData)->execute();
                continue;
            }
            $contentValues[$fieldName] = $this->getFieldWithoutHandler->value($translation)->execute();
        }

        $content = $this->contentBuilder->createContent($contentValues);

        $this->repository->addFieldsValues($page->name, $this->translation);

        return $content;
    }

    public function page(Content $page) : self
    {
        $this->page = $page;
        return $this;
    }

    public function translation(Translation $translation) : self
    {
        $this->translation = $translation;
        return $this;
    }
}
