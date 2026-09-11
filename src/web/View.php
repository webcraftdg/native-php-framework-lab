<?php

namespace webcraftdg\framework\web;

use webcraftdg\framework\App;
use webcraftdg\framework\exceptions\ViewException;
use ReflectionClass;

class View
{
    const POS_HEAD = 'head';
    const POS_BODY_START = 'bodyStart';
    const POS_BODY_END = 'bodyEnd';
    public array $bundles = [];
    public $scripts = [];
    public $styles = [];


    public function renderPhpFile(string $filname, array $params = []) : string
    {
        if(file_exists($filname) === false) {
            throw new ViewException($filname.' not found');
        }
        extract($params, EXTR_SKIP);
        ob_start();
        require_once $filname;
        return ob_get_clean();
    }


    public function render(
        string $view,
        array $params = []
    ): string {
        $params['view'] = $this;
        $controller = App::$app->getController();
        $filename = $view;
        if ($controller instanceof Controller) {
            $filename = $controller->viewPath.DIRECTORY_SEPARATOR.$filename.'.php';
        }
        //Génération de la vue
        return $this->renderPhpFile(
            $filename,
            $params
        );
    }
    
    public function registerAssetBundle(string $name) : AssetBundle
    {
        $bundle = ($this->bundles[$name]) ?? null;
        if ($bundle === null) {
            $relection = new ReflectionClass($name);
            $bundle = $relection->newInstance(App::$app);
            $depends = $bundle->depends;
            foreach($depends as $depend) {
                $this->registerAssetBundle($depend);
            }
            $bundle->init();
            $this->bundles[$name] = $bundle;
        }
        return $bundle;
    }

    public function metaTag(array $options = [])
    {
        return Html::tag('meta', null, $options);
    }

    public function registerJsfile(string $filename, string $pos = self::POS_HEAD, array $options = []) : void
    {
        $this->scripts[$pos] = ($this->scripts[$pos]) ?? [];
        $options = ($options) ?? [];
        $options['src'] = $filename;
        $this->scripts[$pos][] = $this->regiserJs($options);
    }

    public function registerCssfile(string $filename, string $pos = self::POS_HEAD, array $options = []) : void
    {
        $this->styles[$pos] = ($this->styles[$pos]) ?? [];
        $options = ($options) ?? [];
        $options['href'] = $filename;
        $this->styles[$pos][] = $this->regiserCss($options);
    }

    public function regiserJs(array $options) : string
    {
        return Html::tag('script', '', $options);
    }

    public function regiserCss(array $options) : string
    {
        $options['rel'] = ($options['rel']) ?? 'stylesheet';
        return Html::tag('link', null, $options);
    }

    public function registerFile(string $type, string $filename, string $pos = self::POS_HEAD, array $options = []) : void
    {
        if ($type === 'js') {
            $this->registerJsfile($filename, $pos, $options);
        } elseif ($type === 'css') {
            $this->registerCssfile($filename, $pos, $options);
        }
    }

    public function head() : string
    {
        $content = '';
        $script = ($this->scripts[self::POS_HEAD]) ?? [];
        $style = ($this->styles[self::POS_HEAD]) ?? [];
        $content .= implode("\n", $script);
        $content .= implode("\n", $style);
        return $content;
    }

    public function startPageBody()
    {
        $content = (isset($this->scripts[self::POS_BODY_START])) ? implode("\n", $this->scripts[self::POS_BODY_START]) : '';
        $content .= (isset($this->styles[self::POS_BODY_START])) ? implode("\n", $this->styles[self::POS_BODY_START]) : '';
        return $content;
    }

    public function endPageBody()
    {
        $content = (isset($this->scripts[self::POS_BODY_END])) ? implode("\n", $this->scripts[self::POS_BODY_END]) : '';
        $content .= (isset($this->styles[self::POS_BODY_END])) ? implode("\n", $this->styles[self::POS_BODY_END]) : '';
        return $content;
    }
}
