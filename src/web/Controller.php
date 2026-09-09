<?php

namespace webcraftdg\framework\web;

use webcraftdg\framework\base\ApplicationContext;

class Controller
{
        public ?string $viewPath = null;
        public ?string $layoutPath = null;
        public string $layout = 'main';
        private View $view;
        
        public function __construct(
            protected ApplicationContext $appContext,
            public string $id
        )
        {}

        public function init()
        {
            $this->viewPath = $this->getViewPath();
            $this->layoutPath = $this->getLayoutsPath();
            $this->view = new View();
        }

        public function render(string $view, array $params = []) : string
        {
            $content =  $this->view->render($view, $params);
                // 2. Génération du layout
            return $this->view->renderPhpFile(
                $this->layoutPath . '/main.php',
                array_merge(
                    $params,
                    ['content' => $content]
                )
            );
        }

        public function getViewPath()
        {
            if ($this->viewPath === null) {
                $this->viewPath = $this->appContext->getViewPath().DIRECTORY_SEPARATOR.$this->id;
            }
            return $this->viewPath;
        }

        public function getLayoutsPath()
        {
            if ($this->layoutPath === null) {
                $this->layoutPath = $this->appContext->getViewPath().DIRECTORY_SEPARATOR.'layouts';
            }
            return $this->layoutPath;
        }
}
