<?php

declare(strict_types=1);

namespace Marussia\Page\Actions;

use Marussia\Page\Repositories\PageRepository;
use Marussia\Paget\Page;

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
