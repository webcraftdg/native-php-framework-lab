<?php
/**
 * 
 */
namespace webcraftdg\framework\base\validation;

use webcraftdg\framework\base\Model as BaseModel;
use webcraftdg\framework\interfaces\ValidatorInterface;
use webcraftdg\framework\base\validation\validators\EmailValidator;
use webcraftdg\framework\base\validation\validators\MatchValidator;
use webcraftdg\framework\base\validation\validators\RequiredValidator;
use webcraftdg\framework\base\validation\validators\StringValidator;
use webcraftdg\framework\exceptions\ValidationException;

final class CoreValidation
{

    public function __construct(
        public BaseModel $model
    )
    {
    }


    /**
     * validate
     *
     * @param  array $attributes
     * @param  array $rules
     *
     * @return void
     */
    public function validate(array $attributes, array $rules) : void
    {
        $validatorName = array_shift($rules);
        if (empty($validatorName) === true) {
            throw new ValidationException('Validation model, validateur non présent ou mal formatté');
        }
        $validator = $this->instanciateValidator($validatorName);

        foreach($attributes as $attribute) {
            if ($this->model->hasAttribute($attribute) === false) {
                throw new ValidationException('Validation model: property : '.$attribute.' not exist in ');
            }
            $validator->validate($this->model, $attribute, $rules);
        }
    }

    public function instanciateValidator(string $name) : ValidatorInterface | null
    {
        return match($name) {
            'string' => new StringValidator(),
            'email' => new EmailValidator(),
            'required' => new RequiredValidator(),
            'match' => new MatchValidator(),
            default => null
        };
    }
}