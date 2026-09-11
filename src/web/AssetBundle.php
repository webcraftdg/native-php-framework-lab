<?php

namespace webcraftdg\framework\web;

use webcraftdg\framework\App;
use webcraftdg\framework\base\ApplicationContext;
use webcraftdg\framework\web\View;

class AssetBundle
{
    public string $baseUrl;
    protected AssetSource $assetSource;
    public string $sourcePath;
    public string $strategy;
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
        $this->sourcePath = ($this->sourcePath) ?? $this->appContext->getBasePath().DIRECTORY_SEPARATOR.$this->pathRelatif;
        $this->assetSource = new AssetSource(
            path:$this->sourcePath,
            strategy:($this->strategy) ?? AssetSource::STRATEGY_CATALOG,
            catalogFile:$this->assetCatalogFilename
        );
    }

    /**
     * register view
     *
     * @param  \webcraftdg\framework\web\View $view
     *
     * @return AssetBundle
     */
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
        if ($this->assetSource->strategy === AssetSource::STRATEGY_CATALOG) {
            $this->publishCatalog($am, $view);
        } elseif($this->assetSource->strategy === AssetSource::STRATEGY_PATTERN) {
            $this->publishPattern($am, $view);
        }
    }

    /**
     * publish
     *
     * @param  AssetManager                   $am
     * @param  \webcraftdg\framework\web\View $view
     *
     * @return void
     */
    protected function publishCatalog(AssetManager $am, View $view)
    {
        $catalogfile = DIRECTORY_SEPARATOR.trim($this->sourcePath, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.$this->assetCatalogFilename;
        if (file_exists($catalogfile) === true) {
            $catalog = json_decode(file_get_contents($catalogfile));
            foreach($catalog as $name => $params) {
                foreach($params as $type => $path) {
                    $filePath = $this->assetSource->path.DIRECTORY_SEPARATOR.$this->distDirectory.DIRECTORY_SEPARATOR.$path;
                    $checkName = $this->preparePublish($am, $filePath, $name, $type, $path);
                    if ($checkName === true) {
                        $pos = View::POS_HEAD;
                        $options = $this->getAssetTypeOptions($type);
                        $pos = ($options['pos']) ?? $pos;
                        unset($options['pos']);
                        $view->registerFile(
                            type:$type,
                            filename:$am->baseUrl.'/'.trim($path, '/'),
                            pos:$pos,
                            options:$options
                        );
                    }
                }
            }
        }
    }

    /**
     * get asset options
     *
     * @param  string $type
     *
     * @return array
     */
    private function getAssetTypeOptions(string $type) : array
    {
        if ($type === 'js') {
            $options = ($this->jsOptions) ?? [];
        } else {
            $options = ($this->cssOptions) ?? [];
        }
        return $options;
    }
    /**
     * publish pattern
     *
     * @param  AssetManager                   $am
     * @param  \webcraftdg\framework\web\View $view
     *
     * @return void
     */
    protected function publishPattern(AssetManager $am, View $view)
    {
        $this->publishType(
            am:$am,
            view:$view,
            assetFiles:$this->js,
            type:'js',
            options:$this->jsOptions
        );
           $this->publishType(
            am:$am,
            view:$view,
            assetFiles:$this->css,
            type:'css',
            options:$this->cssOptions
        );
    }


      protected function publishType(
        AssetManager $am,
        View $view,
        array $assetFiles,
        string $type = 'js',
        array $options = []
    )
    {
        if (file_exists($this->assetSource->path) === true) {
            $pathFiles = scandir($this->assetSource->path);
            foreach($assetFiles as $assetFile) {
                $name = pathinfo($assetFile, PATHINFO_FILENAME);
                $pattern = '/^'.$name.'((-)*(\w)*)/';
                foreach($pathFiles as $pathFile) {
                    if (preg_match($pattern, $pathFile, $mathes) === 1) {
                        $path = '/'.$type.'/'.trim($pathFile, '/');
                        $filePath = $this->assetSource->path.DIRECTORY_SEPARATOR.$pathFile;
                        $checkName = $this->preparePublish($am, $filePath, $assetFile, $type, $path);
                        if ($checkName === true) {
                            $pos = View::POS_HEAD;
                            $options = ($options) ?? [];
                            $pos = ($options['pos']) ?? $pos;
                            unset($options['pos']);
                            $view->registerFile(
                                type:$type,
                                filename:$am->baseUrl.$path,
                                pos:$pos,
                                options:$options
                            );
                        }
                    }
                }
            }
        }
    }

    /**
     * prepare publishing
     *
     * @param  AssetManager $am
     * @param  string       $filePath
     * @param  string       $name
     * @param  string       $type
     * @param  string       $path
     *
     * @return bool
     */
    private function preparePublish(
        AssetManager $am,
        string $filePath,
        string $name,
        string $type,
        string $path) : bool
    {
        $checkName = false;
        if (file_exists($filePath) === true) {
            $assetPath = $am->assetsPath;
            if (file_exists($assetPath) === false) {
                mkdir($assetPath);
            }
            $destPath = $assetPath.DIRECTORY_SEPARATOR.$type;
            if (file_exists($destPath) === false) {
                mkdir($destPath);
            }
            $destFilePath = $assetPath.DIRECTORY_SEPARATOR.$path;
            $pos = View::POS_HEAD;
            if ($type === 'js') {
                $checkName = in_array($name, $this->js);
                $pos = ($this->jsOptions['pos']) ?? $pos;
            } else {
                $checkName = in_array($name, $this->css);
                $pos = ($this->cssOptions['pos']) ?? $pos;
            }
            if ($checkName === true) {
                $checkName = copy($filePath, $destFilePath);
            }
        }
        return $checkName;
    }
}
