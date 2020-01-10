<?php

declare(strict_types=1);

namespace Marussia\Content\Actions;

use Marussia\Content\TableBuilders\PageBuilder;
use Marussia\Content\Exceptions\CreatePageTableActionException;

class CreatePagesTableAction
{
    private $builder;
    
    public function __construct(PageBuilder $builder)
    {
        $this->builder = $builder;
    }

    public function execute()
    {
        try {
            $this->builder->beginTransaction();
            $this->builder->createPagesTable();
            $this->builder->createFieldsTable();
            $this->builder->commit();
        } catch (\Throwable $exception) {
            $this->builder->rollBack();
            throw new CreatePageTableActionException($exception);
        }
    }
}
