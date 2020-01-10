<?php

declare(strict_types=1);

namespace Marussia\Content\TableBuilders;

class PageBuilder
{
    use NameBuilderTrait;
    use DataTypeBuilderTrait;

    private $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function beginTransaction() : self
    {
        $this->pdo->beginTransaction();
        return $this;
    }

    public function commit() : self
    {
        $this->pdo->commit();
        return $this;
    }

    public function rollBack() : void
    {
        $this->pdo->rollBack();
    }

    public function createPageValuesTable(string $pageName)
    {
        $valuesTableName = $this->makeValuesTableName($pageName);

        $sql = 'CREATE TABLE IF NOT EXISTS ' . $valuesTableName . '(' .
            'id SERIAL PRIMARY KEY, ' .
            'title VARCHAR(255) NOT NULL, ' .
            'language VARCHAR(10) NOT NULL)';

        $result = $this->pdo->prepare($sql);

        $result->execute();
    }

    public function createPagesTable()
    {
        $sql = 'CREATE TABLE IF NOT EXISTS pages (' .
            'id SERIAL PRIMARY KEY, ' .
            'name VARCHAR(255) NOT NULL UNIQUE, ' .
            'slug VARCHAR(255) NOT NULL, ' .
            'options JSONB, ' .
            'is_active BOOLEAN DEFAULT TRUE, ' .
            'is_deleted BOOLEAN DEFAULT FALSE' .
            'created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(), ' .
            'updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW())';

        $this->pdo->exec($sql);
    }

    public function createFieldsTable()
    {
        $sql = 'CREATE TABLE IF NOT EXISTS pages_fields (' .
            'id SERIAL PRIMARY KEY, ' .
            'page_id SERIAL, ' .
            'name VARCHAR(255) NOT NULL UNIQUE, ' .
            'type VARCHAR(255) NOT NULL, ' .
            'title VARCHAR(255) NOT NULL, ' .
            'options JSONB, ' .
            'is_active BOOLEAN DEFAULT TRUE, ' .
            'hidden BOOLEAN DEFAULT FALSE)';

        $this->pdo->exec($sql);
    }

    public function createFieldValue(string $pageName, string $fieldName, array $dataType) : bool
    {
        $valuesTableName = $this->makeValuesTableName($pageName);
        
        $type = $this->prepareType($dataType);
        
        $sql = 'ALTER TABLE ' . $valuesTableName . ' ' .
               'ADD COLUMN IF NOT EXISTS ' . $fieldName . ' ' . $type;
               
        $result = $this->pdo->prepare($sql);
               
        return $result->execute() > 0;
    }
}
