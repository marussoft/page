<?php

declare(strict_types=1);

namespace Marussia\Pagest\Actions;

use Marussia\Pages\Repositories\PageRepository;
use Marussia\Contracts\ActionInterface;
use Marussia\Collection;

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
