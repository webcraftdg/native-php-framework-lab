<?php
/**
 * 
 */
namespace webcraftdg\framework\base;

use webcraftdg\framework\exceptions\ApplicationException;

class ApplicationContext
{
    const APPLICATION_TYPE_WEB = 'web';
    const APPLICATION_TYPE_CONSOLE = 'console';
    public string $basePath;
    public string $viewPath;
    public string $layoutPath;
    

    public function getBasePath() : string
    {
        if (isset($this->basePath) === false) {
            throw new ApplicationException('basePath is mandatory in config file', 400);
        }
        return $this->basePath;
    }

    public function getViewPath()
    {
        if (isset($this->viewPath) === false) {
            $this->viewPath = $this->getBasePath().DIRECTORY_SEPARATOR.'views';
        }
        return $this->viewPath;
    }

    public function getLayoutsPath()
    {
        if (isset($this->layoutPath) === false) {
            $this->layoutPath = $this->getBasePath().DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'layouts';
        }
        return $this->layoutPath;
    }

}