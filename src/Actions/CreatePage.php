<?php

declare(strict_types=1);

namespace Marussia\Pages\Actions;

use Marussia\Pages\TableBuilders\PageBuilder;
use Marussia\Pages\Repositories\PageRepository;
use Marussia\Pages\PageFactory;
use Marussia\Pages\Exceptions\CreatePageActionException;
use Marussia\Pages\Exceptions\SlugInvalidCharactersException;
use Marussia\Pages\PageBuilder;

class CreatePage
{
    private $builder;

    private $repository;

    private $pageFactory;

    private $PageValue;

    private $pageName = '';

    private $slug = '';

    private $title = '';

    private $options = [];

    public function __construct(PageBuilder $builder, PageRepository $repository, PageFactory $factory, PageBuilder $PageValue)
    {
        $this->builder = $builder;
        $this->repository = $repository;
        $this->pageFactory = $factory;
        $this->PageValue = $PageValue;
    }

    public function execute() : bool
    {
        if (strlen($this->pageName) === 0) {
            throw new PageNameNotSetException;
        }

        if (strlen($this->slug) === 0) {
            throw new SlugNotSetException;
        }

        if (strlen($this->title) === 0) {
            throw new TitleNotSetException;
        }

        try {
            $this->builder->beginTransaction();
            $this->builder->createPageValuesTable($this->pageName);
            $this->builder->commit();
        } catch (\Throwable $exception) {
            $this->builder->rollBack();
            throw new CreatePageActionException($exception);
        }
        $page = $this->factory->create($this->pageName, $this->slug, $this->title, $this->options);
        $content = $this->contentValue->create(['title' => $this->title, 'language' => $this->language]);
        $this->repository->addFieldsValues($this->pageName, $content);
        return $this->repository->addPage($page);
    }

    public function name(string $pageName) : self
    {
//         if (preg_match('/.*/', $pageName)) { // ошибка в регулярке
//             throw new PageNameInvalidCharactersException($pageName);
//         }

        $this->pageName = $pageName;
        return $this;
    }

    public function slug(string $slug) : self
    {
//         if (preg_match('/[^0-9_]/i', $slug)) {
//             throw new SlugInvalidCharactersException($slug);
//         }

        $this->slug = $slug;
        return $this;
    }

    public function title(string $title) : self
    {
        $this->title = $title;
        return $this;
    }

    public function options(array $options) : self
    {
        $this->options = $options;
        return $this;
    }

    public function language(string $language) : self
    {
        $this->language = $language;
        return $this;
    }
}
