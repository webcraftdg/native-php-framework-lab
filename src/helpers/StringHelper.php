<?php

namespace webcraftdg\framework\helpers;

use InvalidArgumentException;

class StringHelper
{
    public static function checkNoNumber(string $value) : string
    {
        if (preg_match('/\d/', $value)) {
            throw new InvalidArgumentException('La valeur ne doit pas contenir de chiffre.');
        }
        return $value;
    }

    public static function camelToSeparator(string $value, string $separator = '-'): string
    {
        $value = static::checkNoNumber($value);
        return preg_replace(
            '/([a-z])([A-Z])/',
            '$1' . $separator . '$2',
            $value
        );
    }

    public static function basename(string $value, string $suffix = '') : string
    {
        return basename($value, $suffix);
    }
}
