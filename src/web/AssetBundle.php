<?php

namespace webcraftdg\framework\web;

use webcraftdg\framework\App;
use webcraftdg\framework\base\ApplicationContext;
use webcraftdg\framework\web\View;
use webcraftdg\framework\exceptions\ViewException;

class AssetBundle
{
    public string $baseUrl;
    public string $sourcePath;
    public string $assetCatalogFilename = 'assets-catalog.json';
    public string $distDirectory = 'dist';
    public array $js = [];
    public array $css = [];
    public array $jsOptions = [];
    public array $cssOptions = [];
    public array $depends = [];
    public string $pathRelatif = 'assets'.DIRECTORY_SEPARATOR.'webpack/';

      
    public function __construct(
        protected ApplicationContext $appContext
    )
    {}


    public function init()
    {
        $this->sourcePath = $this->appContext->getBasePath().DIRECTORY_SEPARATOR.$this->pathRelatif;
    }

    public static function register(View $view) : AssetBundle
    {
        $assetManager = App::$app->getAssetManager();
        $bundle =  $view->registerAssetBundle(\get_called_class());
        $bundle->publish($assetManager, $view);
        $bundle->baseUrl = $assetManager->baseUrl;
        return $bundle;
    }

    public function publish(AssetManager $am, View $view)
    {
        $catalogfile = DIRECTORY_SEPARATOR.trim($this->sourcePath, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.$this->assetCatalogFilename;
        if (file_exists($catalogfile) === true) {
            $catalog = json_decode(file_get_contents($catalogfile));
            foreach($catalog as $name => $params) {
                foreach($params as $type => $path) {
                    $filePath = $this->sourcePath.DIRECTORY_SEPARATOR.$this->distDirectory.DIRECTORY_SEPARATOR.$path;
                    if (file_exists($filePath) === true) {
                        $this->preparePublish($am, $view, $filePath, $name, $type, $path);
                    }
                }
            }
              
         
        }
    }

    private function preparePublish(AssetManager $am, View $view,string $filePath, string $name, string $type, string $path) : void
    {
        $assetPath = $am->assetsPath;
        if (file_exists($assetPath) === false) {
            mkdir($assetPath);
        }
        $destPath = $assetPath.DIRECTORY_SEPARATOR.$type;
        if (file_exists($destPath) === false) {
            mkdir($destPath);
        }
        $destFilePath = $assetPath.DIRECTORY_SEPARATOR.$path;
        $pos = 'head';
        if ($type === 'js') {
            $checkName = in_array($name, $this->js);
            $pos = ($this->jsOptions['pos']) ?? $pos;
        } else {
            $checkName = in_array($name, $this->css);
            $pos = ($this->cssOptions['pos']) ?? $pos;
        }
        if ($checkName === true) {
            copy($filePath, $destFilePath);
            $view->registerFile($type, $am->baseUrl.'/'.trim($path, '/'));
        }
    }
}
