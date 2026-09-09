<?php
/**
 * 
 */
namespace webcraftdg\framework\base;

use webcraftdg\framework\base\validation\CoreValidation;
use ReflectionClass;
use ReflectionProperty;

class Model {

    public array $errors = [];

    /**
     * scenario
     *
     * @return array
     */
    public function scenario() : array
    {
        return [];
    }

    public function validationRules() : array
    {
        return [];
    }

    public function getFormName()
    {
        $reflector = new ReflectionClass($this);
        return $reflector->getShortName();
    }

    public function load(array $data) : bool
    {
        $data = ($data[$this->getFormName()]) ?? $data;
        $this->setAttributes($data);
        return true;
    }
    
    public function validate() : bool
    {

        $validationRules = $this->validationRules();
        $coreValidator = new CoreValidation($this);
        foreach($validationRules as $rule) {
            $attributes = ($rule[0]) ?? null;
            unset($rule[0]);
            $attributes = (is_array($attributes) === false) ? [$attributes] : $attributes;
            $coreValidator->validate($attributes, $rule);
        }
        return $this->hasErrors() === false;
    }

    public function getAttributes() : array
    {
        $relexion = new ReflectionClass($this);
        $properties   = $relexion->getProperties(ReflectionProperty::IS_PUBLIC);
        $attributes = [];
        foreach ($properties as $prop) {
            $attributes[] = $prop;
        }
        return $attributes;
    }

    /**
     * set attributes
     *
     * @param  array $attributes
     *
     * @return array
     */
    public function setAttributes(array  $attributes = []) : array
    {
        foreach($attributes as $attribute => $value) {
            if($this->hasAttribute($attribute) === true) {
                $this->$attribute = $value;
            }
        }
        return $attributes;
    }

    /**
     * has attribute
     *
     * @param  string $attribute
     *
     * @return bool
     */
    public function hasAttribute(string $attribute) : bool
    {
        return property_exists($this, $attribute);
    }

    /**
     * add Error
     *
     * @param  string $attribute
     * @param  string $error
     *
     * @return void
     */
    public function addError(string $attribute, string $error) : void
    {
        $this->errors[$attribute] = $error;
    }

    /**
     * has errors
     *
     * @param  mixed $attribute
     *
     * @return bool
     */
    public function hasErrors(mixed $attribute = null) : bool
    {
        if ($attribute !== null) {
            $hasError = empty($this->errors[$attribute]) === false;
        } else {
            $hasError = empty($this->errors) === false;
        }
        return $hasError;
    }

    /**
     * get error
     *
     * @param  string $attribute
     *
     * @return string
     */
    public function getError(string $attribute) : string
    {
        return ($this->errors[$attribute]) ?? '';
    }
}