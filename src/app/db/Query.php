<?php

namespace contacts\app\db;

use contacts\exceptions\QueryException;
use PdoException;

class Query
{
    private string $startSql;
    private string $conditionSql;
    private string $sql;
    private array $conditions;
    private array $params;

    public function __construct(
        private string $table,
        private string $verb = 'SELECT',
        private array $attributes = ['*'],
        private array $joins = []
    )
    {
    }
    
    public function getSql()
    {
        return $this->sql;
    }

    public function getParams()
    {
        return ($this->params) ?? [];
    }
    public function addParams(array $params)
    {
        $params = ($this->params) ?? [];
        foreach($params as $attribute => $value) {
            $params[$attribute] = $value;
        }
        $this->params = $params;
    }

    public function where(mixed $condition) : Query
    {
        return $this->baseWhere('AND', $condition);
    }

    public function orWhere(mixed $condition) : Query
    {
        return $this->baseWhere('OR', $condition);
    }

    public function andWhere(mixed $condition) : Query
    {
        return $this->baseWhere('AND', $condition);
    }

    public function buildSql() : Query
    {
        $startSql = $this->beginSql();
        $conditionSql = $this->endSql();
        $this->sql = $startSql.' '.$conditionSql;
        return $this;
    }


    protected static $availableConditionsVerbs = [
        'AND', 'OR'
    ];

    protected function beginSql()
    {
        $this->startSql = $this->verb.' ';
        foreach($this->attributes as $sourceAttribute => $targetAttribute) {
            if (is_string($sourceAttribute) && empty($targetAttribute) === false) {
                $this->startSql .= $sourceAttribute.' AS '.$targetAttribute;
            } else {
                $this->startSql .= $targetAttribute;
            }
            $this->startSql .= ' ';
        }
        $this->startSql .= ' FROM '.$this->table;
        foreach($this->joins as $queryJoin) {
            $sqlJoin = $this->buildQueryJoin($queryJoin);
            $subSql = $sqlJoin->getSql();
            $this->addParams($sqlJoin->getParams());
            $this->startSql .= $subSql.' ';
        }
        return $this->startSql;
    }

    protected function endSql() : string
    {
        $conditionSql = '';
        if (empty($this->conditions) === false) {
            $conditionSql .= ' WHERE ';
            foreach($this->conditions as $verb => $conditions) {
                $conditionSql .= implode(' '.strtoupper($verb).' ', $conditions);
                $conditionSql .= ' ';
            }
        }
        $this->conditionSql = $conditionSql;
        return $this->conditionSql ;
    }
    
    protected function buildQueryJoin(mixed $queryJoin) : QueryJoin
    {
        if (is_array($queryJoin) === true and count($queryJoin) === 3) {
            $queryJoin = new QueryJoin(
                type:$queryJoin[0],
                onTable:$queryJoin[1],
                conditions:$queryJoin[2]
            );
        } elseif ($queryJoin instanceof QueryJoin === false) {
            throw new QueryException('Join statement Array or QueryJoin is available', 400);
        }
        return $queryJoin;
    }


    protected function baseWhere(string $verb, mixed $conditions) : Query
    {
        if (in_array(strtoupper($verb), self::$availableConditionsVerbs) === false) {
            throw new PdoException('Sql: verb '.$verb.' not available');
        }
        if (empty($conditions) === false) {
            $globalConditions = ($this->conditions[strtoupper($verb)]) ?? [];
            $cleanConditions = static::cleanCondition($conditions);
            foreach($cleanConditions as $attribute => $value) {
                $globalConditions[] = $attribute.'=:'.$attribute;
                $this->params[':'.$attribute] = $value;
            }
            $this->conditions[strtoupper($verb)] = $globalConditions;
        }
        
        return $this;
    }

    public static function cleanCondition(mixed $conditions) : array
    {
        $newConditions = [];
        if (is_string($conditions) === true) {
            $conditions = static::conditionStringtoArray($conditions);
        } 
        
        if (is_array($conditions) === true) {
            foreach($conditions as $attribute => $value) {
                if (empty($value) === true && $value != '0') {
                    $subConditions = static::conditionStringtoArray($attribute);
                    foreach($subConditions as $subAttribute => $subValue) {
                        $newConditions[$subAttribute] = $subValue;
                    }
                } else {
                    $newConditions[$attribute] = $value;
                }
            }
        }
        return $newConditions;
    }

    public static function conditionStringtoArray(string $condition) : array
    {
        $parts = explode('=', trim($condition));
        $newcondition = [];
        if (count($parts) === 2) {
            $newcondition[$parts[0]] = $parts[1];
        } else {
            $newcondition[] = $condition;
        }
        return $newcondition;
    }
}
