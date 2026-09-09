<?php

namespace webcraftdg\framework\web;

class AssetManager
{
    public string $baseUrl;
    public string $assetsPatch;
    public function __construct($config = [])
    {
        $this->baseUrl = ($this->baseUrl) ?? '/assets';
        $this->assetsPatch = ($this->assetsPatch) ?? dirname(__DIR__, 3).'/www/assets/';
        $this->baseUrl = ($config['baseUrl']) ?? $this->baseUrl;
        $this->assetsPatch = ($config['assetsPatch']) ?? $this->assetsPatch;
    }


}
