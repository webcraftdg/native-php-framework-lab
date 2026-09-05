<?php

namespace contacts\app\web;

use contacts\App;

class Url
{
    public static function to(mixed $route): string
    {
        $urlManager = App::$app->getUrlManager();
        return $urlManager->createUrl($route);
    }
}
