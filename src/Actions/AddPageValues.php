<?php

declare(strict_types=1);

namespace Marussia\Pages\Actions;

use Marussia\Pages\Repositories\PageRepository;
use Marussia\Pages\Page;

class AddPageValuesAction
{
    private $repository;

    public function __construct(PageRepository $repository)
    {
        $this->repository = $repository;
    }

    public function execute(string $pageName, Content $content) : Content
    {
        return $this->repository->addFieldsValues($pageName, $content);
    }
}
