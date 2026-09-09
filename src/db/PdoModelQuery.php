<?php

namespace webcraftdg\framework\db;

class PdoModelQuery extends Query
{



    /**
     * constructor
     *
     * @param  string $className
     * @param  string $verb
     * @param  array  $attributes
     * @param  array  $joins
     */
    public function __construct(
        private string $className,
        private string $verb = 'SELECT',
        private array $attributes = ['*'],
        private array $joins = []
    )
    {

        parent::__construct(
            table:$className::getTableName(),
            verb:$verb,
            attributes:$attributes,
            joins:$joins
        );
    }


    /**
     * Pdo model all
     *
     * @param  array $params
     *
     * @return array
     */
    public function all(array $params = []) : array
    {
        $records = parent::all($params);
        return array_map(function (array $record){
            $model = new $this->className();
            $model->isNewRecord = false;
            $model->setAttributes($record);
            return $model;
        }, $records);
    }

    /**
     * pdo model one
     *
     * @param  array         $params
     *
     * @return PdoModel|null
     */
    public function one(array $params = []) : ?PdoModel
    {
        $modelPdo = null;
        $record =  parent::one($params);
         if ($record !== false) {
            $modelPdo = new $this->className();
            $modelPdo->isNewRecord = false;
            $modelPdo->setAttributes($record);
        }
        return $modelPdo;
    }
    
}
