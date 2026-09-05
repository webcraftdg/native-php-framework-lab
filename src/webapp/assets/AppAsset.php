<?php

namespace contacts\webapp\assets;

use contacts\app\web\AssetBundle;

class AppAsset extends AssetBundle
{

    public string $basePath;
    public string $sourcePath;

    public array $js = [
        'manifest',
        'main'
    ];

    public array $css = [
        'main'
    ];
}