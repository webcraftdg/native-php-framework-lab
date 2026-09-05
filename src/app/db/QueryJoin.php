<?php

namespace contacts\app\db;

use contacts\exceptions\PdoException;

class QueryJoin
{
    private string $sql;
    private array $params;
    
    public function __construct(
        private string $type,
        private string $onTable,
        private mixed $conditions
    )
    { 
        if (in_array(strtolower($type), ['inner', 'left', 'right']) === false) {
            throw new PdoException($type.' : in joins not available');
        }
        $this->initSql();
    }

    public function getConditions() : array
    {
        return $this->conditions;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getSql()
    {
        return $this->sql;
    }

    public function getParams()
    {
        return $this->params;
    }

    public function initSql() :void
    {
    
        $sql = strtoupper($this->type).' JOIN '.$this->onTable.' ON ';
        $cleanConditions = Query::cleanCondition($this->conditions);
        $sqlCondition = '';
        foreach($cleanConditions as $attribute => $value) {
            $sqlCondition .= $attribute.'=:'.$attribute;
            $this->params[':'.$attribute] = $value;
            $sqlCondition .= ' AND ';
        }
        $sql .= trim($sqlCondition, 'AND');
        $this->sql = $sql;
    }
}