<?php
/**
 * 
 */
namespace contacts\app\db;

use contacts\App;
use contacts\app\base\Model;
use contacts\app\helpers\StringHelper;

class PdoModel extends Model
{
    public bool $isNewRecord = true;

    public static function getTableName() : string
    {
          return StringHelper::camelToSeparator(StringHelper::basename(get_called_class()));
    }

    public static function getDbconnection() : Connection
    {
        return App::$app->getDbConnection();
    }

    public static function getPrimaryKey(): array
    {
        return static::getDbconnection()->getPrimaryKeys(static::getTableName());
    }


    public static function findOne(mixed $condition) : ?static
    {
        $cleanConditions = $condition;
        $result = null;
        $primaryKey = static::getPrimaryKey();
        if (is_array($condition) === false && count($primaryKey) === 1) {
            $cleanConditions = [$primaryKey[0] => $condition];
        }
        $records = static::findRawAll($cleanConditions);
        if (count($records) === 1) {
            $modelPdo = new static();
            $modelPdo->isNewRecord = false;
            $modelPdo->setAttributes($records[0]);
            $result = $modelPdo;
        }
        return $result;
    }

    public static function findAll($condition = [], $params = []) : array
    {
        $records = static::findRawAll($condition, $params);
        return array_map(function (array $record) {
            $model = new static();

            $model->isNewRecord = false;
            $model->setAttributes($record);

            return $model;
        }, $records);
    }

    public static function findRawAll(mixed $condition, $params = []) : array
    {

        $connection = App::$app->getDbConnection();
        $table = static::getTableName();
        $baseQuery = static::prepareQuery(
            tableName:$table,
            verb:'SELECT'
        );
        $baseQuery->where($condition);
        $sql = $baseQuery->buildSql()->getSql();
        $sqlParams = $baseQuery->getParams();
        $params = $params + $sqlParams;
        $pdo = $connection->prepare($sql);
        $pdo->execute($params);

        return $pdo->fetchAll();
    }

    public static function prepareQuery(string $tableName, string $verb = 'SELECT', $attributes = ['*'], $joins = []) : Query
    {
        return new Query(
            table:$tableName,
            verb:$verb,
            attributes:$attributes,
            joins:$joins
        );
    }

    public function getAttributes(): array
    {
        $connection = $this->getDbconnection();
        return $connection->getFilterProperties($this->getTableName());
    }

    public function save($runValidate = false, mixed $attributes = null) : bool
    {
        $success = true;
        $connection = $this->getDbconnection();
        if($runValidate === true) {
            $success = $this->validate();
        }
        if ($success === true) {
            $data = $this->prepareToSave($attributes);
            if ($this->isNewRecord === true) {
                $data = $connection->insert($this->getTableName(), $data);
            } else {
                $data = $connection->update($this->getTableName(), $data);
            }
            if (is_array($data) === true) {
                $this->setAttributes($data);
            } else {
                $success = false;
            }
        }
        return $success;
    }

    protected function prepareToSave(mixed $attributes = null) : array
    {
        $availableAttributes = $this->getAttributes();
        if (is_array($attributes) === true) {
            $availableAttributes = array_values(
                array_intersect($availableAttributes, $attributes)
            );
        }
        $data = [];
        foreach($availableAttributes as $attribute) {
            if ($this->hasAttribute($attribute) === true) {
                $data[$attribute] = $this->$attribute;
            }
        }
        return $data;
    }

}