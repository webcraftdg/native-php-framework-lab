<?php

namespace webcraftdg\framework\interfaces;

use webcraftdg\framework\base\Model;

interface ValidatorInterface
{
    public function validate(Model $model, string $attribute, array $rules) : void;
}