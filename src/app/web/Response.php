<?php

namespace contacts\app\web;

class Response
{
    public function __construct(
        private string $content = '',
        private int $statusCode = 200,
        private array $cookies = [],
        private array $headers = []
    ) {
        $this->headers += [
            'Content-Type' => 'text/html; charset=utf-8',
        ];
    }

    public function send(): void
    {
        http_response_code($this->statusCode);

        foreach ($this->headers as $name => $value) {
            header("$name: $value");
        }

        echo $this->content;
    }

    public function addHeader(string $name, string $value)
    {
        $this->headers[$name] = $value;
    }
    public function removeHeader(string $name)
    {
        unset($this->headers[$name]);
    }
    public function getHeaders()
    {
        return $this->headers;
    }

    public function setStatusCode(int $code)
    {
        $this->statusCode = $code;
    }
}