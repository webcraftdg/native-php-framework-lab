<?php

namespace contacts\app\web;

use contacts\App;
use contacts\exceptions\ViewException;
use ReflectionClass;

class View
{
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
            $filename = $controller->viewPath.$controller->id.'/'.$filename.'.php';
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
            $bundle = $relection->newInstance();
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

    public function registerJsfile(string $filename, string $pos = 'head') : void
    {
        $this->scripts[$pos] = ($this->scripts[$pos]) ?? [];
        $this->scripts[$pos][] = Html::tag('script', '', ['src' => $filename]);
    }

    public function registerCssfile(string $filename, string $pos = 'head') : void
    {
        $this->styles[$pos] = ($this->styles[$pos]) ?? [];
        $this->styles[$pos][] = Html::tag('link', null, ['href' => $filename, 'rel' => 'stylesheet']);
    }

    public function registerFile(string $type, string $filename, string $pos = 'head') : void
    {
        if ($type === 'js') {
            $this->registerJsfile($filename, $pos);
        } elseif ($type === 'css') {
            $this->registerCssfile($filename, $pos);
        }
    }

    public function head() : string
    {
        $content = '';
        $script = ($this->scripts['head']) ?? [];
        $style = ($this->styles['head']) ?? [];
        $content .= implode("\n", $script);
        $content .= implode("\n", $style);
        return $content;
    }

    public function endPage()
    {
        $content = (isset($this->scripts['end'])) ? implode("\n", $this->scripts['end']) : '';
        $content .= (isset($this->styles['end'])) ? implode("\n", $this->styles['end']) : '';
        return $content;
    }
}
