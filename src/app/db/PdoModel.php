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

    public static function find() : PdoModelQuery
    {
        return static::internalFind();
    }

    /**
     * find one
     *
     * @param  mixed       $condition
     *
     * @return static|null
     */
    public static function findOne(mixed $condition) : ?static
    {
        $cleanConditions = $condition;
        $primaryKey = static::getPrimaryKey();
        if (is_array($condition) === false && count($primaryKey) === 1) {
            $cleanConditions = [$primaryKey[0] => $condition];
        }
        $query = static::findRaw($cleanConditions);
        return $query->pdoModelOne();
    }

    /**
     * find all
     *
     * @param  array $condition
     * @param  array $params
     *
     * @return array
     */
    public static function findAll($condition = [], $params = []) : array
    {
        $query = static::findRaw($condition, $params);
        return $query->all($params);
    }

    /**
     * internal find
     *
     * @return PdoModelQuery
     */
    protected static function internalFind() : PdoModelQuery
    {
        return static::prepareQuery(
            className: get_called_class(),
            verb:'SELECT'
        );
    }

    protected static function findRaw(mixed $condition, $params = []) : PdoModelQuery
    {

        $baseQuery = static::internalFind();
        $baseQuery->where($condition);
        return $baseQuery;
    }

    public static function prepareQuery(string $className, string $verb = 'SELECT', $attributes = ['*'], $joins = []) : PdoModelQuery
    {
        return new PdoModelQuery(
            className:$className,
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