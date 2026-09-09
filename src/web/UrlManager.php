<?php

namespace webcraftdg\framework\web;


class UrlManager
{
    public function __construct(
        public string $baseUrl,
        public string $prefix = '',
        private array $rules = []
    ) {
    }

    public function parseUrl(string $url): mixed
    {
        $path = trim(parse_url($url, PHP_URL_PATH), '/');
        $route = $url;
        foreach ($this->rules as $rule) {
            $pattern = $rule['pattern'];
            if (empty($this->prefix) || str_starts_with($path, $this->prefix)) {
                $path = substr($path, strlen($this->prefix));
            }

            //je récupère les paramètres du pattern
            preg_match_all(
                '/<([^:]+):([^>]+)>/',
                $pattern,
                $matchesParamsConfig
            );

            $pattern = $this->patternToRegex($pattern);

            $pattern = '/'.str_replace('/', '\/', $pattern).'/';

            if (preg_match($pattern, $path, $matches)) {
                $params = [];
                $index = 0;
                foreach($matches as $key => $paramValue) {
                    if ($key > 0 && isset($matchesParamsConfig[1][$index]) === true) {
                        $params[$matchesParamsConfig[1][$index]] = $paramValue;
                        $index ++;
                    }
                }

                $route = [
                    'route' => $rule['target'],
                    'params' => $params,
                ];
            }
        }
        return $route;
    }

    public function createUrl(mixed $route) : mixed
    {
        $finaleUrl = $route;
        if (is_array($route) === true) {
            $params = $route;
            $finaleUrl = array_shift($params);
        } else {
            $params = [];
        }
        $parsedRoute = $this->parseRoute($finaleUrl);
        if (is_array($parsedRoute) === true) {
            $finaleUrl = $parsedRoute[0];
            $params += $parsedRoute[1];
        }
        $parts = explode('/', trim($finaleUrl, '/'));
        if (count($parts) === 1) {
            $newParts = [
                $finaleUrl,
                'default'
            ];
            $finaleUrl = implode('/', $newParts);
        }
        foreach($this->rules as $rule) {
            if ($rule['target'] === trim($finaleUrl, '/')) {
                if (preg_match('/<([^:]+):([^>]+)>/', $rule['pattern'], $patternMatches) === 1) {
                    $finaleUrl = preg_replace_callback(
                        '/<([^:]+):([^>]+)>/',
                        function($match) use ($params){
                            return ($params[$match[1]]) ?? null;
                        },
                        $rule['pattern']
                    );
                } else {
                    $finaleUrl = $rule['pattern'];
                    $lineParams = null;
                    if (empty($params) === false) {
                        foreach($params as $key => $value) {
                            $lineParams .= $key.'='.$value.'&';
                        }
                    }
                    if ($lineParams !== null) {
                        $finaleUrl .= '?'.trim($lineParams, '&');
                    }
                }
            }
        }
        return '/'.trim($finaleUrl, '/');
    }

    private function patternToRegex(string $pattern) : string
    {
         return preg_replace_callback(
            '/<([^:]+):([^>]+)>/',
            fn($match) => $match[2],
            $pattern
        );
    }

    private function parseRoute(string $route) : mixed
    {
        $parts = explode('?', $route);
        if (count($parts) > 1) {
            $result  = [];
            $result[] = $parts[0];
            $params = explode('&', $parts[1]);
            $cleanParams = [];
            foreach($params as $paramAndValue) {
                $items = explode('=', $paramAndValue);
                if (count($items) === 2) {
                    $cleanParams[$items[0]] = $items[1];
                }
            }
            $result[] = $cleanParams;
            $route = $result;
        }
        return $route;
    }
}
