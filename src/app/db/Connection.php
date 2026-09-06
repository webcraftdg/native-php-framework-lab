<?php

namespace contacts\app\db;

use PDO;
use PDOException;

class Connection
{
    public PDO $pdo;

    public function __construct($config = [])
    {
        $this->pdo = new PDO(
            $config['dsn'],
            $config['username'],
            $config['password'],
            $config['options'] ?? []
        );
    }

    public function prepare(string $sql): \PDOStatement
    {
        return $this->pdo->prepare($sql);
    }

    public function query(string $sql): \PDOStatement
    {
        return $this->pdo->query($sql);
    }

    public function lastInsertId(): string|false
    {
        return $this->pdo->lastInsertId();
    }

    public function getPdo(): PDO
    {
        return $this->pdo;
    }

    public function executeQuery(Query $query)
    {
        $stmt = $this->prepare($query->getSql());
        $params = ($query->getParams()) ?? [];
        return $stmt->execute($params);
    }


    public function insert(string $tableName, array $data): array | false
    {
        $columns = array_keys($data);
        $values = array_map(
            fn(string $column) => ':' . $column,
            $columns
        );

        $sql = sprintf(
            'INSERT INTO `%s` (%s) VALUES (%s)',
            $tableName,
            implode(', ', $columns),
            implode(', ', $values)
        );

        $stmt = $this->prepare($sql);
        $success = $stmt->execute($data);

        if ($success) {
            $lastInsertedId = $this->pdo->lastInsertId();
            //Call get primary after get primary ke
            $primaryKeys = $this->getPrimaryKeys($tableName);
            if (count($primaryKeys) === 1) {
                $data[$primaryKeys[0]] = $lastInsertedId;
            }
        } else {
            $data = false;
        }

        return $data;
    }

    public function update(string $tableName, array $data): array | false
    {
        $primaryKeys = $this->getPrimaryKeys($tableName);
        $cleanValues = array_filter(
            $data,
            fn($key) => !in_array($key, $primaryKeys),
            ARRAY_FILTER_USE_KEY
        );
        $values = array_map(
            fn(string $column) => $column.' = :'.$column,
            array_keys($cleanValues)
        );
        $conditions = array_map(
            fn(string $key) =>  "`$key` = :$key",
            $primaryKeys
        );
        if(count($conditions) === 1) {
            $conditions = $conditions[0];
        } else {
            $conditions = implode(' AND ', $conditions);
        }
        $sql = sprintf(
            'UPDATE `%s` SET %s WHERE %s',
            $tableName,
            implode(', ', $values),
            $conditions
        );

        $stmt = $this->prepare($sql);
        $success = $stmt->execute($data);
        return $data;
    }


    public function getRawProperties(string $tableName)
    {
        $stmt = $this->query('DESCRIBE '.$tableName);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getFilterProperties(string $tableName, mixed $filters = 'Field') : array
    {
        $propertiesFiltered = [];
        $rawProperties = $this->getRawProperties($tableName);
        $filters = (is_string($filters) === true) ? [$filters] : $filters;
        foreach($rawProperties as $rawPropertyValues) {
            $propertiesFiltered[] = $this->buildProperties($rawPropertyValues, $filters);
        }
        return $propertiesFiltered;
    }

    protected function buildProperties(array $rawPropertyValues, mixed $filters) : mixed
    {
        $propertyValues = [];
        foreach($filters as $filterName) {
            if (isset($rawPropertyValues[$filterName]) === true) {
                $propertyValues[] = $rawPropertyValues[$filterName];
            }
        }
        return (count($propertyValues) === 1) ? $propertyValues[0] : $propertyValues;
    }

    public function getPrimaryKeys(string $table): array
    {
        $this->validateIdentifier($table);

        $stmt = $this->pdo->query(
            "SHOW KEYS FROM `$table` WHERE Key_name = 'PRIMARY'"
        );

        $keys = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return array_column($keys, 'Column_name');
    }

    private function validateIdentifier(string $identifier): void
    {
        if (!preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $identifier)) {
            throw new PDOException(
                "Identifiant SQL invalide : $identifier"
            );
        }
    }
    
}
