<?php 

namespace contacts\app\web;

use contacts\App;
use contacts\exceptions\HttpException;
use ReflectionClass;
use ReflectionMethod;
use contacts\exceptions\RouterException;

class Router
{

    public function __construct(
        private Request $request,
        private string $controllerNamespace
    )
    { }


    public function resolve(): array
    {

        $uri = parse_url($this->request->getUri(), PHP_URL_PATH);
        $uri = trim($uri, '/');
        $parsedUri = App::$app->getUrlManager()->parseUrl($uri);
        $params = [];
        if (is_string($parsedUri) === true) {
            $parts = explode('/', $parsedUri);
        } else {
            $parts = explode('/', $parsedUri['route']);
            $params = ($parsedUri['params']) ?? [];
        }
        $controllerId = $parts[0] ?: 'default';
        $action = $parts[1] ?? 'default';
        $actionMethod = ucfirst($action).'Action';
        return [
            $controllerId,
            $actionMethod,
            $params
        ];
    }

    public function dispatch(): mixed
    {

        list($controllerId, $actionMethod, $parametres) = $this->resolve();

        $controllerClass =
            $this->controllerNamespace
            . '\\'
            . ucfirst($controllerId)
            . 'Controller';

        if (!class_exists($controllerClass)) {
            throw new HttpException('Class : '.$controllerId.' not found', 404);
        }
        $reflection = new ReflectionClass($controllerClass);

        $controllerInstance = $reflection->newInstance($controllerId);
        $controllerInstance->init();

        if (!method_exists($controllerInstance, $actionMethod)) {
            throw new HttpException('Action : '.$actionMethod.' not found', 404);
        }

        App::$app->setController($controllerInstance);

        $method = new ReflectionMethod(
            $controllerInstance,
            $actionMethod
        );

        $params = [];

        foreach ($method->getParameters() as $parameter) {
            $name = $parameter->getName();
            $value = ($parametres[$name]) ?? null;
            if($value === null) {
                $value = $this->request->getQueryParam($name, null);
            }
            if ($value !== null) {
                $params[] = $value;
            }
        }

        $result =  $method->invokeArgs(
            $controllerInstance,
            $params
        );
        if ($result === null) {
            throw new RouterException('Url Nor found');
        }
        return $result;
    }
}