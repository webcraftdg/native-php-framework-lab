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
    public static function beginForm(array $options = []):string
    {
        $tag = '<form ';
        $parsedOptions = static::parseTagOptions($options);
        $tag .= implode(' ', $parsedOptions);
        $tag .= '>';
        return $tag;
    }
    public static function endform()
    {
        return '</form>';
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

    public static function labelModelPdo(PdoModel $pdoModel, string $attribute, array $options = []) : string
    {
        $options['for'] = ($options['for']) ?? static::inputPdoModelId($pdoModel, $attribute);
        $class = ($options['class']) ?? '';
        $label = ($options['label']) ?? $attribute;
        return static::tag('label', $label, $options);
    }

    public static function inputModelPdo(PdoModel $pdoModel, string $attribute, array $options = []) : string
    {
        $options['name'] = ($options['name']) ?? static::inputPdoModelName($pdoModel, $attribute);
        $options['id'] = ($options['id']) ?? static::inputPdoModelId($pdoModel, $attribute);
        $options['value'] = ($options['value']) ?? $pdoModel->$attribute;
        $class = ($options['class']) ?? '';
        $errorTag = null;
        $optionTagError = ($options['addErrorTag']) ?? null;
        unset($options['addErrorTag']);
        if($pdoModel->hasErrors($attribute) ===true) {
            $options['class'] = $class.' error';
            if ($optionTagError !== null) {
                if (is_callable($optionTagError) === true) {
                    $errorTag = call_user_func($optionTagError);
                } elseif($optionTagError === true) {
                    $errorTag = Html::tag('span', $pdoModel->getError($attribute), ['class' => 'error-message']);
                }
            }
        }
        return static::input('input', $options).$errorTag;
    }
    
    protected static function parseTagOptions(array $options = []) : array
    {
        $cleanOptions = [];
        foreach($options as $key => $value) {
            if (is_bool($value) === true) {
               $cleanOptions[] = static::parseBoolOptions($key, $value);
            } elseif(is_array($value) === true) {
                $cleanOptions[] = $key.'='.json_encode($value);
            } else {
                $cleanOptions[] = $key.'="'.$value.'"';
            }
        }
        return $cleanOptions;
    }

    private static function parseBoolOptions(string $key, mixed $value) : string
    {
        $cleanOption = '';
        if ($key !== 'disabled') {
            $cleanOption = $key.'="'.(($value) ? 'true' : 'false').'"';
        } else {
            if ($value === true) {
                $cleanOption = $key;
            }
        }
        return $cleanOption;
    }
}