<?php

namespace webcraftdg\framework;

use webcraftdg\framework\base\ApplicationContext;
use webcraftdg\framework\db\Connection;
use webcraftdg\framework\exceptions\ApplicationException;
use webcraftdg\framework\web\AssetManager;
use webcraftdg\framework\web\Controller;
use webcraftdg\framework\web\Request;
use webcraftdg\framework\web\Response;
use webcraftdg\framework\web\Router;
use webcraftdg\framework\web\UrlManager;

final class Application extends ApplicationContext
{

    private Request $request;
    private Connection $dbConnection;
    private Router $router;
    private UrlManager $urlManager;
    private AssetManager $assetManager;
    private Controller $controller;
    public string $controllerNamespace;
    public $language = 'fr';
    public $timezone = 'Europe/Paris';
    public $name = 'Contacts';
    public static $availableTypes = [
       self::APPLICATION_TYPE_WEB, self::APPLICATION_TYPE_CONSOLE
    ];

    public function __construct(protected string $type, array $config = [])
    {
        if (in_array($type, static::$availableTypes) === false) {
            throw new ApplicationException('Type : '.$this->type.' not accepted', 400);
        }
        $this->init($config);
        $this->setPhpParam();
        App::$app = $this;
    }

    public function run() : void
    {
        $response = $this->handleRequest();
        if ($response instanceof Response) {
            $response->send();
        }
    }

    public function init($config = [])
    {
        foreach($config as $moduleName => $configuration) {
            if (isset($configuration['class']) === false) {
                switch($moduleName) {
                    case 'connection':
                        $this->initConnection($configuration);
                        break;
                    case 'request':
                        $this->initRequest($config);
                        break;
                    case 'urlManager':
                         $this->initUrlManager($configuration);
                        break;
                    case 'assetManager':
                         $this->initAssetManager($configuration);
                        break;
                    case static::APPLICATION_TYPE_WEB:
                         $this->initWebParams($configuration);
                        break;
                    default:
                        $this->setParam($moduleName, $configuration);
                        break;
                }
            }
        }
        $this->router = new Router($this->request, $this->controllerNamespace);
    }

    public function setController(Controller $controller)
    {
        $this->controller = $controller;
    }
    
    public function getController() : ?Controller
    {
        return ($this->controller) ?? null;
    }
    public function getRequest() : Request
    {
        return $this->request;
    }

    public function getDbConnection():Connection
    {
        return $this->dbConnection;
    }
    
    public function getUrlManager() : UrlManager
    {
        return $this->urlManager;
    }

     public function getAssetManager() : AssetManager
    {
        return $this->assetManager;
    }

    public function handleRequest(): Response | null
    {

        $response = $this->router->dispatch();
        if (is_string($response) === true) {
            $response = new Response($response);
        }
        return $response;
    }

    protected function setParam(string $name, mixed $config)
    {
        if (property_exists($this, $name) === true) {
            $this->$name = $config;
        }
    }
    protected function setPhpParam() : mixed
    {
        $language = match($this->language) {
            'fr' => setlocale(LC_ALL, 'fr_FR.UTF-8'),
            default => setlocale(LC_ALL, 'fr_FR.UTF-8')
        };
        date_default_timezone_set($this->timezone);
        return $language;
    }

    protected function initWebParams($config = [])
    {
        foreach($config as $attribute => $value) {
            if (property_exists($this, $attribute) === true) {
                $this->$attribute = $value;
            }
        }
    }
    protected function initConnection($config = [])
    {
        $this->dbConnection = new Connection($config);
    }

    protected function initRequest($config = [])
    {
        $this->request = new Request($config);
    }

    protected function initAssetManager($config = [])
    {
        $this->assetManager = new AssetManager($config);
    }

    protected function initUrlManager($config = [])
    {
        $this->urlManager = new UrlManager(
            $config['baseUrl'] ?? '',
            $config['prefix'] ?? '',
            $config['rules'] ?? []
        );
    }
}
