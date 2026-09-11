<?php

namespace webcraftdg\framework\web;

use webcraftdg\framework\base\ApplicationContext;

class AssetManager
{
    public string $baseUrl;
    public string $assetsPath;
    public string $assetsDirectoryName = 'assets';
    public function __construct(
        protected ApplicationContext $applicationContext,
        $config = []
    )
    {
        $this->baseUrl = ($this->baseUrl) ?? '/'.$this->assetsDirectoryName;
        $this->assetsPath = $this->applicationContext->getBaseWebPath().DIRECTORY_SEPARATOR.$this->assetsDirectoryName;
        $this->baseUrl = ($config['baseUrl']) ?? $this->baseUrl;
        $this->assetsPath = ($config['assetsPatch']) ?? $this->assetsPath;
    }
}
