<?php

namespace contacts\app\web;

use Exception;

class Request
{
    private array $headers = [];
    private string $uri;
    private string $requestKey = 'abcdef';

    public function __construct(array $config = [])
    {
        $this->requestKey = ($config['requestKey']) ?? $this->requestKey;
    }

    public function getMethod(): string
    {
        return $_SERVER['REQUEST_METHOD'] ?? 'GET';
    }

    public function getQueryParam(?string $key, mixed $default = null): mixed
    {
        
        return $_GET[$key] ?? $default;
    }

    
    public function post(?string $key = null, mixed $default = null): mixed
    {
        if ($key === null) {
            return $_POST;
        }

        return $_POST[$key] ?? $default;
    }

    public function getBodyParams(): array
    {
        $contentType = $this->getHeader('Content-Type', '');

        if (str_contains($contentType, 'application/json')) {
            $body = $this->body();

            if ($body === null) {
                return [];
            }

            $data = json_decode($body, true);

        }

        if (str_contains($contentType, 'application/x-www-form-urlencoded')) {
            $data = $this->post();
        }

        if (str_contains($contentType, 'multipart/form-data')) {
            $data = $this->post();
        }
        return is_array($data) ? $data : [];
    }

    public function body(): string | null
    {
        return file_get_contents('php://input') ?: null;
    }

    public function json(): array
    {
        $data = json_decode($this->body(), true);

        return is_array($data) ? $data : [];
    }

    public function getHeaders(): array
    {
        $this->headers = getallheaders();
        return $this->headers;
    }

    public function getHeader(string $name, mixed $default = null): mixed
    {
        foreach ($this->getHeaders() as $key => $value) {
            if (strcasecmp($key, $name) === 0) {
                return $value;
            }
        }
        return $default;
    }

    public function addHeader(string $name, mixed $value)
    {
        if (isset($this->headers[$name]) === false) {
            $this->headers[$name] = $value;
        }
    }


    public function getUri(): string
    {
        $this->uri =  $_SERVER['REQUEST_URI'] ?? '/';
        return $this->uri;
    }

    public function isGet(): bool
    {
        return $this->getMethod() === 'GET';
    }

    public function isPost(): bool
    {
        return $this->getMethod() === 'POST';
    }

    public function isPut(): bool
    {
        return $this->getMethod() === 'PUT';
    }

    public function isDelete(): bool
    {
        return $this->getMethod() === 'DELETE';
    }
}
