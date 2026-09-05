<?php

namespace contacts\app\interfaces;

use contacts\app\base\Model;

interface ValidatorInterface
{
    public function validate(Model $model, string $attribute, array $rules) : void;
}