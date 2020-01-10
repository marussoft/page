<?php

declare(strict_types=1);

namespace Marussia\Content\Actions;

use Marussia\Content\Repositories\PageRepository;
use Marussia\Contracts\ActionInterface;
use Marussia\Content\Collection;

class GetPageListAction extends AbstractAction implements ActionInterface
{
    protected $repository;

    public function __construct(PageRepository $repository)
    {
        $this->repository = $repository;
    }

    public function execute() : Collection
    {
        return $this->repository->getAll($this->language);
    }
}
