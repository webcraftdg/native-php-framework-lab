<?php
/**
 * 
 */

namespace webcraftdg\framework\base\validation\validators;

use webcraftdg\framework\base\Model;
use webcraftdg\framework\interfaces\ValidatorInterface;
use webcraftdg\framework\exceptions\ValidationException;

final class StringValidator implements ValidatorInterface
{
    /**
     * validate
     *
     * @param  Model $model
     * @param  string                   $attribute
     * @param  array                    $rules
     *
     * @return void
     */
    public function validate(Model $model, string $attribute, array $rules) : void
    {
        if ($model->hasAttribute($attribute) === false) {
            throw new ValidationException('Validation model: property : '.$attribute.' not exist in ');
        }
        $message = (isset($rules['message']) === true) ? str_replace('{{attribute}}', $attribute, $rules['message']) : $attribute.' is not string';
        if (is_string($model->$attribute) === false) {
            $model->addError($attribute, $message);
        }
    }
}