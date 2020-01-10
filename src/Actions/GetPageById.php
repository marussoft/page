<?php

declare(strict_types=1);

namespace Marussia\Content\Actions;

use Marussia\Content\Repositories\PageRepository;
use Marussia\Content\Actions\Providers\FillFieldProvider as ActionProvider;
use Marussia\Content\Content;
use Marussia\Content\ContentBuilder;
use Marussia\Contracts\ActionInterface;

class GetPageByIdAction extends AbstractAction implements ActionInterface
{
    protected $repository;

    protected $actionProvider;

    protected $contentBuilder;

    private $pageId;

    public function __construct(PageRepository $repository, ActionProvider $actionProvider, ContentBuilder $contentBuilder)
    {
        $this->repository = $repository;
        $this->actionProvider = $actionProvider;
        $this->contentBuilder = $contentBuilder;
    }

    public function execute() : ?Content
    {
        $page = $this->repository->getPageById($this->pageId);

        if ($page === null) {
            return $page;
        }

        $fields = $this->repository->getFields($page->id);
        $fieldsValues = $this->repository->getFieldsValues($page->name, $this->language);

        $contentData = [];

        foreach ($fieldsValues as $fieldName => $value) {

            if ($fields->has($fieldName)) {
                $fieldData = $this->actionProvider->createFieldData($fields->get($fieldName));
                $fieldData->value = $value;
                $contentData[$fieldName] = $this->actionProvider->fillField($fieldData);
                continue;
            }
            $contentData[$fieldName] = $this->actionProvider->createFieldWithoutHandler($fieldName, $value);
        }

        $contentData['id'] = $page->id;
        $contentData['name'] = $page->name;
        $contentData['slug'] = $page->slug;
        $contentData['is_active'] = $page->isActive;
        $contentData['options'] = $page->options;
        
        return $this->contentBuilder->createContent($contentData);
    }

    public function pageId(int $pageId) : self
    {
        $this->pageId = $pageId;
        return $this;
    }
}
