<?php

declare(strict_types=1);

namespace Marussia\Pages\Actions;

use Marussia\Pages\Actions\Providers\FillFieldProvider as ActionProvider;
use Marussia\Pages\Repositories\PageRepository;
use Marussia\Pages\Content;
use Marussia\Pages\ContentBuilder;
use Marussia\Contracts\ActionInterface;

class GetPageBySlugAction extends AbstractAction implements ActionInterface
{
    private $repository;

    private $actionProvider;

    private $contentBuilder;

    private $slug;

    public function __construct(PageRepository $repository, ActionProvider $actionProvider, ContentBuilder $contentBuilder)
    {
        $this->repository = $repository;
        $this->contentBuilder = $contentBuilder;
        $this->actionProvider = $actionProvider;
    }

    public function execute() : ?Content
    {
        if ($this->slug === null) {
            throw new SlugForPageNotReceiptedExceptions;
        }

        $page = $this->repository->getPageBySlug($this->slug);

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
        $contentData['title'] = $page->title;
        $contentData['is_active'] = $page->isActive;
        $contentData['options'] = $page->options;

        return $this->contentBuilder->createContent($contentData);
    }

    public function slug(string $slug) : self
    {
        $this->slug = $slug;
        return $this;
    }
}
