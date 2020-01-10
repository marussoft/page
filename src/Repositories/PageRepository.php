<?php

declare(strict_types=1);

namespace Marussia\Content\Repositories;

use Marussia\Content\Content;
use Marussia\Content\Collection;
use Marussia\Content\PageFactory;
use Marussia\Content\Entities\Page;
use Marussia\Content\TableBuilders\NameBuilderTrait;

class PageRepository
{
    use NameBuilderTrait;

    private $pdo;

    private $pageFactory;

    public function __construct(\PDO $pdo, PageFactory $pageFactory)
    {
        $this->pdo = $pdo;
        $this->pageFactory = $pageFactory;
    }

    public function getPageById(int $pageId) : ?Page
    {
        $sql = 'SELECT * FROM pages WHERE id = ?';

        $result = $this->pdo->prepare($sql);

        $result->execute([$pageId]);

        $page = null;

        if ($result->execute([$pageId])) {
            $pageData = $result->fetch(\PDO::FETCH_ASSOC);
            $page = $this->pageFactory->createFromArray($pageData);
        }

        return $page;
    }

    public function getPageBySlug(string $pageSlug) : ?Page
    {
        $sql = 'SELECT * FROM pages WHERE slug = :slug';

        $result = $this->pdo->prepare($sql);

        $result->bindParam(':slug', $pageSlug, \PDO::PARAM_STR);

        $page = null;

        if ($result->execute()) {

            $pageData = $result->fetch(\PDO::FETCH_ASSOC);

            if ($pageData !== false) {
                $page = $this->pageFactory->createFromArray($pageData);
            }
        }

        return $page;
    }

    public function getFields(int $pageId) : Collection
    {
        $sql = 'SELECT * FROM pages_fields WHERE page_id = ?';

        $result = $this->pdo->prepare($sql);

        $result->execute([$pageId]);

        $fields = $result->fetchAll(\PDO::FETCH_ASSOC);

        $fieldCollection = new Collection;

        if ($fields !== null) {
            foreach ($fields as $field) {
                $fieldCollection->set($field['name'], $field);
            }
        }

        return $fieldCollection;
    }

    public function getFieldsValues(string $pageName, string $language) : Collection
    {
        $valuesTable = $this->makeValuesTableName($pageName);

        $sql = 'SELECT * FROM ' . $valuesTable . ' ' .
               'WHERE language = :language';

        $result = $this->pdo->prepare($sql);

        $result->bindParam(':language', $language, \PDO::PARAM_STR);

        $result->execute();

        $data = $result->fetch(\PDO::FETCH_ASSOC);

        if ($data === false) {
            return new Collection;
        }

        return new Collection($data);
    }

    public function addPage(Page $page) : bool
    {
        $sql = 'INSERT INTO pages (name, slug, title, options) VALUES (:name, :slug, :title, :options)';

        $result = $this->pdo->prepare($sql);

        $result->bindParam(':name', $page->name, \PDO::PARAM_STR);
        $result->bindParam(':slug', $page->slug, \PDO::PARAM_STR);
        $result->bindParam(':title', $page->title, \PDO::PARAM_STR);
        $result->bindParam(':options', $page->options, \PDO::PARAM_STR);
        return $result->execute();
    }

    public function addFieldsValues(string $pageName, Content $content) : Content
    {
        $columns = '';
        $values = '';

        foreach ($content as $fieldName => $value) {
            $columns .= $fieldName . ', ';
            $values .= ':' . $fieldName . ', ';
        }

        $valuesTable = $this->makeValuesTableName($pageName);
        $sql = 'INSERT INTO ' . $valuesTable . ' (' . substr($columns,0,-2)  . ') VALUES (' . substr($values,0,-2) . ')';

        $result = $this->pdo->prepare($sql);

        $type = \PDO::PARAM_STR;

        foreach ($content as $key => &$value) {

            if (is_int($value)) {
                $type = \PDO::PARAM_INT;
            } elseif (is_bool($value)) {
                $type = \PDO::PARAM_BOOL;
            }

            $result->bindParam(':' . $key, $value, $type);
        }

        $result->execute();

        $content->id  = $result->lastInsertId();
        return $content;
    }

    public function getAll(string $language = 'en-en') : Collection
    {
        $sql = 'SELECT * FROM pages';

        $result = $this->pdo->prepare($sql);

        $collection = new Collection;

        if ($result->execute()) {
            $rawData = $result->fetchAll(\PDO::FETCH_ASSOC);

            foreach ($rawData as $data) {

                $valuesTable = $this->makeValuesTableName($data['name']);
                
                $titleQuery = 'SELECT title FROM ' . $valuesTable . ' WHERE language = :language';
                
                $titleResult = $this->pdo->prepare($titleQuery);
                
                $titleResult->bindValue(':language', $language);
                
                $titleResult->execute();
                
                $data['title'] = $titleResult->fetchColumn();
                
                $page = $this->pageFactory->createFromArray($data);
                
                $collection->set($page->name, $page);
            }
        }

        return $collection;
    }

    public function updatePage(int $pageId, bool $isActive) : void
    {
        $sql = 'UPDATE pages SET is_active = :is_active, updated_at = NOW() WHERE id = :page_id';

        $result = $this->pdo->prepare($sql);
        $result->bindParam(':is_active', $isActive, \PDO::PARAM_BOOL);
        $result->bindParam(':page_id', $pageId, \PDO::PARAM_INT);
        $result->execute();
    }


    public function updatePageValues(string $pageName, array $content, string $language) : void
    {
        $fields = '';

        foreach ($content as $fieldName => $value) {
            $fields .= $fieldName . ' = :' . $fieldName . ', ';
        }

        $valuesTable = $this->makeValuesTableName($pageName);
        $sql = 'UPDATE ' . $valuesTable . ' SET ' . substr($fields,0,-2) . ' WHERE language = :language';

        $result = $this->pdo->prepare($sql);

        $type = \PDO::PARAM_STR;

        foreach ($content as $key => &$value) {

            if (is_int($value)) {
                $type = \PDO::PARAM_INT;
            } elseif (is_bool($value)) {
                $type = \PDO::PARAM_BOOL;
            }

            $result->bindParam(':' . $key, $value, $type);
        }

        $result->bindParam(':language', $language, \PDO::PARAM_STR);

        $result->execute();
    }
}
