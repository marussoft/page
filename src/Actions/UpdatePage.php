<?php

declare(strict_types=1);

namespace Marussia\Content\Actions;

use Marussia\Contracts\ActionInterface;
use Marussia\Content\Repositories\PageRepository;
use Marussia\ContentField\Actions\GetFieldWithoutHandler;
use Marussia\ContentField\Actions\CreateFieldDataAction;
use Marussia\Content\Content;
use Marussia\Content\ContentBuilder;

class UpdatePageAction extends AbstractAction implements ActionInterface
{
    private $repository;

    private $getFieldWithoutHandler;

    private $createFieldData;

    private $contentBuilder;

    private $page;

    private $data = [];

    public function __construct(PageRepository $repository, CreateFieldDataAction $createFieldData, GetFieldWithoutHandler $getFieldWithoutHandler, ContentBuilder $contentBuilder)
    {
        $this->repository = $repository;
        $this->createFieldData = $createFieldData;
        $this->getFieldWithoutHandler = $getFieldWithoutHandler;
        $this->contentBuilder = $contentBuilder;
    }

    public function execute() : Content
    {
        if ($this->page === null) {
            throw new PageIdForUpdateNotReceiptedException;
        }

        $fields = $this->repository->getFields($this->page->id);

        $contentValues = [];

        foreach ($this->data as $fieldName => $updateData) {
        
            if (property_exists($this->page, $fieldName) === false) {
                unset($this->data[$fieldName]);
                continue;
            }

            if ($fields->has($fieldName)) {
                $fieldData = $this->createFieldData->data($fields->get($fieldName))->execute();
                $fieldData->value = $updateData;
                $contentValues[$fieldName] = $this->createFieldInput->fieldData($fieldData)->execute();
                continue;
            }

            $contentValues[$fieldName] = $this->getFieldWithoutHandler->value($updateData)->execute();
        }

        $content = $this->contentBuilder->createContent($contentValues);
        
        if (isset($this->data['is_active']) && $this->data['is_active'] === 'on') {
            unset($this->data['is_active']);
            $this->page->isActive = true;
        }
        
        if ($content->isValid() && count($this->data) > 0) {
            $this->repository->updatePageValues($this->page->name, $this->data, $this->page->language->value);
            $this->repository->updatePage($this->page->id, $this->page->isActive );
        }

        return $content;
    }

    public function page(Content $page) : self
    {
        $this->page = $page;
        return $this;
    }

    public function update(array $data) : self
    {
        $this->data = $data;
        return $this;
    }
}
