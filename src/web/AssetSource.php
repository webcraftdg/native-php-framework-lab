<?php

namespace webcraftdg\framework\web;

use webcraftdg\framework\exceptions\AssetException;

class AssetSource
{
    const STRATEGY_CATALOG = 'catalog';
    const STRATEGY_PATTERN = 'pattern';

    public static $availableStategy = [
        self::STRATEGY_CATALOG,
        self::STRATEGY_PATTERN
    ];


    public function __construct(
        public string $path,
        public string $strategy = 'catalog',
        public ?string $catalogFile = null,
    ) {
        if(in_array($this->strategy, self::$availableStategy) === false) {
            throw new AssetException('Stratégy : '.$this->strategy.' not available', 400);
        }
    }
}
