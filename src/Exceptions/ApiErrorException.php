<?php

namespace Emailit\Exceptions;

class ApiErrorException extends \Exception
{
    private int $httpStatus;
    private string $httpBody;
    private ?array $jsonBody;
    private array $httpHeaders;

    public function __construct(
        string $message,
        int $httpStatus = 0,
        string $httpBody = '',
        ?array $jsonBody = null,
        array $httpHeaders = [],
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, $httpStatus, $previous);
        $this->httpStatus = $httpStatus;
        $this->httpBody = $httpBody;
        $this->jsonBody = $jsonBody;
        $this->httpHeaders = $httpHeaders;
    }

    public static function factory(
        string $message,
        int $httpStatus,
        string $httpBody,
        ?array $jsonBody,
        array $httpHeaders,
    ): static {
        return match (true) {
            $httpStatus === 401 => new AuthenticationException($message, $httpStatus, $httpBody, $jsonBody, $httpHeaders),
            $httpStatus === 429 => new RateLimitException($message, $httpStatus, $httpBody, $jsonBody, $httpHeaders),
            $httpStatus === 422 => new UnprocessableEntityException($message, $httpStatus, $httpBody, $jsonBody, $httpHeaders),
            in_array($httpStatus, [400, 404], true) => new InvalidRequestException($message, $httpStatus, $httpBody, $jsonBody, $httpHeaders),
            default => new self($message, $httpStatus, $httpBody, $jsonBody, $httpHeaders),
        };
    }

    public function getHttpStatus(): int
    {
        return $this->httpStatus;
    }

    public function getHttpBody(): string
    {
        return $this->httpBody;
    }

    public function getJsonBody(): ?array
    {
        return $this->jsonBody;
    }

    public function getHttpHeaders(): array
    {
        return $this->httpHeaders;
    }
}
