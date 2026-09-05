<?php
/**
 * 
 */
require_once __DIR__ . '../../vendor/autoload.php';
require_once __DIR__.'../../src/app/config/bootstrap.php';
$config = require_once __DIR__.'../../src/app/config/config.php';
use contacts\app\Application;
$app = new Application($config);
$app->run();
