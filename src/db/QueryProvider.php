<?php

namespace webcraftdg\framework\db;

use webcraftdg\framework\App;
use webcraftdg\framework\web\Request;

class QueryProvider
{
    

    private int $offset = 0;
    private int $limit;
    private int $totalCount = 0;
    private int $pageNumber = 1;
    private array $models = [];
    private string $queryParamName = 'page';
    private Request $request;

    public function __construct(
        private Query $query,
        private int $pageSize = 20,
        private int $page = 1
    )
    {
        $this->limit = $this->pageSize;
        $this->request = App::$app->getRequest();
        $this->prepareQuery();
    }

    protected function prepareQuery()
    {
        $this->totalCount = $this->query->count();
        $this->computePageNumber();
    }

    public function getModels()
    {
        $this->page = $this->request->getQueryParam($this->queryParamName, $this->page);
        $this->computeOffset();
        $this->query->setLimit($this->limit);
        $this->query->setOffset($this->offset);
        return $this->query->all();
    }

    
    public function getPageSize() : int
    {
        return $this->pageSize;
    }

    public function setPageSize(int $pageSize) : void
    {
        $this->pageSize = $pageSize;
        $this->limit = $pageSize;
    }

    public function getPageNumber() : int
    {
        return $this->pageNumber;
    }

    public function getPage() : int
    {
        return $this->page;
    }
    public function setPage(int $page) : void
    {
        $this->page = $page;
    }

    public function setQueryParamName(string $queryParamName) : void
    {
        $this->queryParamName = $queryParamName;
    }

    public function getQueryParamName() : string
    {
        return $this->queryParamName;
    }

    public function getTotalCount() : int
    {
        return $this->totalCount;
    }

    protected function computeOffset() : void
    {
        $this->offset = ($this->page - 1) * $this->limit;
    }

    protected function computePageNumber() : void
    {
        $this->pageNumber = ceil($this->totalCount / $this->pageSize);
    }
}
