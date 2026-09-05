<?php

namespace contacts\app\web;

use contacts\app\db\PdoModel;

class Html
{


    public static function tag(string $name, string | null $content = null, array $options = []):string
    {
        $tag = '<'.$name.' ';
        $optionsParsed = static::parseTagOptions($options);
        $tag .= implode(' ', $optionsParsed);
        if ($content === null) {
            $tag .= ' />';
        } else {
            $tag .= '>'.$content.'</'.$name.'>';
        }

        return $tag;
    }

    public static function beginTag(string $name, array $options = []):string
    {
        $tag = '<'.$name.' ';
        $parsedOptions = static::parseTagOptions($options);
        $tag .= implode(' ', $parsedOptions);
        $tag .= '>';
        return $tag;
    }


    public static function endTag(string $name)
    {
        return '</'.$name.'>';
    }

    public static function inputPdoModelId(PdoModel $pdoModel, string $attribute) : string
    {
        return $pdoModel->getFormName().'-'.$attribute;
    }

    public static function inputPdoModelName(PdoModel $pdoModel, string $attribute) : string
    {
        return ucfirst($pdoModel->getFormName()).'['.$attribute.']';
    }

    public static function input(string $name, array $options = []) {
        $input = '<'.strtolower($name).' ';
        $parsedOptions = static::parseTagOptions($options);
        $input .= implode(' ', $parsedOptions);
        $input .= '/>';
        return $input;
    }
    public static function inputModelPdo(PdoModel $pdoModel, string $name, string $attribute, array $options = []) : string
    {
        $options['name'] = ($options['name']) ?? static::inputPdoModelName($pdoModel, $attribute);
        $options['id'] = ($options['id']) ?? static::inputPdoModelId($pdoModel, $attribute);
        $options['value'] = ($options['value']) ?? $pdoModel->$attribute;
        $class = ($options['class']) ?? '';
        if($pdoModel->hasErrors() ===true) {
            $options['class'] = $class.' error';
        }
        return static::input($name, $options);
    }
    protected static function parseTagOptions(array $options = []) : array
    {
        $cleanOptions = [];
        foreach($options as $key => $value) {
            if (is_bool($value) === true) {
                $cleanOptions[] = $key.'="'.(($value) ? 'true' : 'false').'"';
            } elseif(is_array($value) === true) {
                $cleanOptions[] = $key.'='.json_encode($value);
            } else {
                $cleanOptions[] = $key.'="'.$value.'"';
            }
        }
        return $cleanOptions;
    }
}