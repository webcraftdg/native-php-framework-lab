<?php

namespace contacts\app\web;


class Controller
{
        public $viewPath;
        public $layoutPath;
        public $layout = 'main';
        private View $view;
        
        public function __construct(public string $id)
        {}

        public function init()
        {
            $this->viewPath = ($this->viewPath) ?? dirname(__DIR__, 2).'/webapp/views/';
            $this->layoutPath = ($this->layoutPath) ??  dirname(__DIR__, 2).'/webapp/views/layouts';
            $this->view = new View();
        }

        public function render(string $view, array $params = []) : string
        {
            $filename = $this->viewPath.$this->id.'/'.$view.'.php';
            $content =  $this->view->render($filename, $params);
                // 2. Génération du layout
            return $this->view->renderPhpFile(
                $this->layoutPath . '/main.php',
                array_merge(
                    $params,
                    ['content' => $content]
                )
            );
        }

}
