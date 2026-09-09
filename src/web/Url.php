<?php

namespace webcraftdg\framework\web;

use webcraftdg\framework\App;

class Url
{
    public static function to(mixed $route): string
    {
        $urlManager = App::$app->getUrlManager();
        return $urlManager->createUrl($route);
    }
}
