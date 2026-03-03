<?php

namespace Emailit;

class ApiResponse
{
    public readonly int $statusCode;
    public readonly array $headers;
    public readonly string $body;
    public readonly ?array $json;

    public function __construct(int $statusCode, array $headers, string $body)
    {
        $this->statusCode = $statusCode;
        $this->headers = $headers;
        $this->body = $body;
        $this->json = json_decode($body, true);
    }
}
