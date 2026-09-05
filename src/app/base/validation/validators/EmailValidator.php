<?php
/**
 * 
 */

namespace contacts\app\base\validation\validators;

use contacts\app\base\Model;
use contacts\app\interfaces\ValidatorInterface;
use contacts\exceptions\ValidationException;

final class EmailValidator implements ValidatorInterface
{
    /**
     * validate
     *
     * @param  \contacts\app\base\Model $model
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
        $message = (isset($rules['message']) === true) ? str_replace('{{attribute}}', $attribute, $rules['message']) : $attribute.' is not email';

        if (filter_var($model->$attribute, FILTER_VALIDATE_EMAIL) === false) {
            $model->addError($attribute, $message);

        }
    }
}