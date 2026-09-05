<?php

namespace contacts\app\web;

use contacts\App;
use contacts\app\web\View;
use contacts\exceptions\ViewException;

class AssetBundle
{
    public string $basePath;
    public string $sourcePath;
    public string $assetCatalogFilename = 'assets-catalog.json';
    public string $distDirectory = 'dist';
    public array $js = [];
    public array $css = [];
    public array $jsOptions = [];
    public array $cssOptions = [];
    public array $depends = [];


    public function init()
    {
        $this->basePath = ($this->basePath) ?? dirname(__DIR__, 2).'/webapp/assets/webpack/';
        $this->sourcePath = ($this->sourcePath) ?? dirname(__DIR__, 2).'/webapp/assets/webpack/';
    }

    public static function register(View $view) : AssetBundle
    {
        $assetManager = App::$app->getAssetManager();
        $bundle =  $view->registerAssetBundle(\get_called_class());
        $bundle->publish($assetManager, $view);
        return $bundle;
    }

    public function publish(AssetManager $am, View $view)
    {

        $catalogfile = DIRECTORY_SEPARATOR.trim($this->sourcePath, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.$this->assetCatalogFilename;
        if (file_exists($catalogfile) === true) {
            try {
                $catalog = json_decode(file_get_contents($catalogfile));
                foreach($catalog as $name => $params) {
                    foreach($params as $type => $path) {
                        $filePath = $this->sourcePath.DIRECTORY_SEPARATOR.$this->distDirectory.DIRECTORY_SEPARATOR.$path;
                        if (file_exists($filePath) === true) {
                            $assetPath = $am->assetsPatch;
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
                }
            } catch (ViewException $e) {
                throw $e;
            }
        }
    }
}
