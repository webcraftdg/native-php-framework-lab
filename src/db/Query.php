<?php

namespace webcraftdg\framework\db;

use webcraftdg\framework\App;
use webcraftdg\framework\exceptions\QueryException;
use webcraftdg\framework\exceptions\PdoException;
use PDOStatement;

class Query
{
    protected string $startSql;
    protected string $conditionSql;
    protected string $sql;
    protected array $conditions;
    protected array $params;
    protected array $sorting;
    protected ?int $limit = null;
    protected ?int $offset = null;


    /**
     * available condition verb
     *
     * @var array
     */
    protected static $availableConditionsVerbs = [
        'AND', 'OR'
    ];


    /**
     * available condition verb
     *
     * @var array
     */
    protected static $availableSorting = [
        'ASC', 'DESC', 'asc', 'desc'
    ];

    /**
     * constructor
     *
     * @param  string $table
     * @param  string $verb
     * @param  array  $attributes
     * @param  array  $joins
     */
    public function __construct(
        private string $table,
        private string $verb = 'SELECT',
        private array $attributes = ['*'],
        private array $joins = []
    )
    {
    }

    /**
     * set attributes
     *
     * @param  array $attributes
     *
     * @return void
     */
    public function setAttributes(array $attributes) : void
    {
        $this->attributes = $attributes;
    }
    
    /**
     * get sql
     *
     * @return string
     */
    public function getSql() : string
    {
        return ($this->sql) ?? '';
    }

    /**
     * get params
     *
     * @return array
     */
    public function getParams()
    {
        return ($this->params) ?? [];
    }

    /**
     * Add params
     *
     * @param  array $params
     *
     * @return void
     */
    public function addParams(array $params)
    {
        $params = ($this->params) ?? [];
        foreach($params as $attribute => $value) {
            $params[$attribute] = $value;
        }
        $this->params = $params;
    }

    /**
     * where => add 'and' condition
     *
     * @param  mixed $condition
     *
     * @return Query
     */
    public function where(mixed $condition) : Query
    {
        return $this->baseWhere('AND', $condition);
    }

    /**
     * orWhere => add 'OR' condition
     *
     * @param  mixed $condition
     *
     * @return Query
     */
    public function orWhere(mixed $condition) : Query
    {
        return $this->baseWhere('OR', $condition);
    }

    /**
     * andWhere => add 'AND' condition
     *
     * @param  mixed $condition
     *
     * @return Query
     */
    public function andWhere(mixed $condition) : Query
    {
        return $this->baseWhere('AND', $condition);
    }

    /**
     * add order by
     *
     * @param  string $attribute
     * @param  string $direction
     *
     * @return Query
     */
    public function addOrderBy(string $attribute, string $direction) : Query
    {
        if ($this->checkSorting(trim($direction)) === false) {
            throw new QueryException('OrderBy : direction not available ', 400);
        }
        $this->sorting[$attribute] = $direction;
        return $this;
    }

    /**
     * order by
     *
     * @param  array $orderBy
     *
     * @return Query
     */
    public function orderBy(array $orderBy) : Query
    {
        foreach($orderBy as $key => $sort) {
            if (is_int($key) === true) {
                $this->sorting[] = $sort;
            } else {
                $this->sorting[$key] = $sort;
            }
        }
        return $this;
    }


    /**
     * Build final sql
     *
     * @return Query
     */
    public function buildSql() : Query
    {
        $startSql = $this->beginSql();
        $finalSql = $this->buildFinalSql($startSql);
        $limit = $this->buildLimitAndOffset();
        $this->sql = $finalSql.' '.$limit;
        return $this;
    }

    /**
     * count
     *
     * @param  array $params
     *
     * @return int
     */
    public function count(array $params = []) : int
    {
        $sql = $this->countSql()->getSql();
        $pdo = $this->preparePdo($sql, $params);
        return (int)$pdo->fetchColumn();
    }

    /**
     * all
     *
     * @param  array $params
     *
     * @return array
     */
    public function all(array $params = []) : array
    {
        $sql = $this->buildSql()->getSql();
        $pdo = $this->preparePdo($sql, $params);
        return $pdo->fetchAll();
    }

    /**
     * one
     *
     * @param  array $params
     *
     * @return mixed
     */
    public function one(array $params = []) : mixed
    {
        $sql = $this->buildSql()->getSql();
        $pdo = $this->preparePdo($sql, $params);
        return $pdo->fetch();
    }
    
    /**
     * set limit
     *
     * @param  int   $limit
     *
     * @return Query
     */
    public function setLimit(int $limit) : Query
    {
        $this->limit = $limit;
        return $this;
    }
    /**
     * set offset
     *
     * @param  int   $offset
     *
     * @return Query
     */
    public function setOffset(int $offset) : Query
    {
        $this->offset = $offset;
        return $this;
    }

    /**
     * count sql
     *
     * @return Query
     */
    protected function countSql() : Query
    {
        $startSql = $this->initialCountSql();
        $finalSql = $this->buildFinalSql($startSql);
        $this->sql = $finalSql;
        return $this;
    }

    /**
     * build final sql
     *
     * @param  string $startSql
     *
     * @return string
     */
    protected function buildFinalSql(string $startSql) : string
    {
        $conditionSql = $this->endSql();
        $sorting = $this->buildOrderBy();
        $sql = $startSql.' '.$conditionSql.$sorting;
        return $sql;
    }

    /**
     * prepare PDO
     *
     * @param  string        $sql
     * @param  array         $params
     *
     * @return \PDOStatement
     */
    protected function preparePdo(string $sql, array $params = []) : PDOStatement
    {
        $connection = App::$app->getDbConnection();
        $sqlParams = $this->getParams();
        $params = $params + $sqlParams;
        $pdo = $connection->prepare($sql);
        $pdo->execute($params);
        return $pdo;
    } 

    /**
     * check sorting
     *
     * @param  string $sorting
     *
     * @return bool
     */
    protected function checkSorting(string $sorting) : bool
    {
        return in_array($sorting, static::$availableSorting);
    }

    /**
     * begin sql => start request sql
     *
     * @return string
     */
    protected function beginSql() : string
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
        $this->startSql .= ' '.$this->buildJoins($this->joins);
        return $this->startSql;
    }


    /**
     * Count SQL
     *
     * @return string
     */
    protected function initialCountSql() : string
    {
        $this->startSql = $this->verb.' ';
        $this->startSql .= ' COUNT(*) FROM '.$this->table;

        $this->startSql .= ' '.$this->buildJoins($this->joins);
        return $this->startSql;
    }

    /**
     * build joins SQL
     *
     * @param  array  $joins
     *
     * @return string
     */
    protected function buildJoins(array $joins) : string
    {
        $joinSql = '';
        foreach($joins as $queryJoin) {
            $sqlJoin = $this->buildQueryJoin($queryJoin);
            $subSql = $sqlJoin->getSql();
            $this->addParams($sqlJoin->getParams());
            $joinSql .= $subSql.' ';
        }
        return $joinSql;
    }


    /**
     * end sql => finalize request sql
     *
     * @return string
     */
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

    /**
     * Build Order by
     *
     * @return string
     */
    protected function buildOrderBy() : ?string
    {
        $sorting = null;
        if (empty($this->sorting) === false) {
            $sorting .= ' ORDER BY '.$this->parseOrderBy($this->sorting);
        }
        return $sorting;
    }

    /**
     * parser sorting
     *
     * @param  array  $sorting
     *
     * @return string
     */
    protected function parseOrderBy(array $sorting) : string
    {
        $mapping = array_map(
            function($attribute, $direction) {
                if (is_int($attribute) === true) {
                    $parts = explode(' ', $direction);
                    $attribute = ($parts[0]) ?? null;
                    $direction = ($parts[1]) ?? null;
                }
                if ($this->checkSorting(trim($direction)) === false) {
                    throw new QueryException('OrderBy : direction not available ', 400);
                } else {
                    return trim($attribute).' '.trim($direction);
                }
            }, array_keys($sorting), array_values($sorting)
        );
        return implode(', ', $mapping);
    }

    
    /**
     * build join 
     *
     * @param  mixed     $queryJoin
     *
     * @return QueryJoin
     */
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

    /**
     * build limit and offset
     *
     * @return string
     */
    protected function buildLimitAndOffset() : string
    {
        $sql = null;
        $sql .= ($this->limit !== null) ? ' LIMIT '.$this->limit : null;
        $sql .= ($this->limit !== null && $this->offset !== null) ? ' OFFSET '.$this->offset : null;
        return $sql;
    }


    /**
     * Base where
     *
     * @param  string $verb
     * @param  mixed  $conditions
     *
     * @return Query
     */
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

    /**
     * clean condition
     *
     * @param  mixed $conditions
     *
     * @return array
     */
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

    /**
     * condition string to array
     *
     * @param  string $condition
     *
     * @return array
     */
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
