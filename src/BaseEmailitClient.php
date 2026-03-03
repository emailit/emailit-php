<?php

namespace Emailit;

use Emailit\Exceptions\ApiConnectionException;
use Emailit\Exceptions\ApiErrorException;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;

class BaseEmailitClient
{
    public const DEFAULT_API_BASE = 'https://api.emailit.com';

    private const SDK_VERSION = '2.0.1';

    private string $apiKey;
    private string $apiBase;
    private GuzzleClient $httpClient;

    /** @var array<string, mixed> */
    private array $config;

    public function __construct(string|array $config)
    {
        if (is_string($config)) {
            $config = ['api_key' => $config];
        }

        if (!isset($config['api_key']) || $config['api_key'] === '') {
            throw new \InvalidArgumentException('api_key is required');
        }

        $defaults = [
            'api_base' => self::DEFAULT_API_BASE,
            'timeout' => 30,
            'connect_timeout' => 10,
        ];

        $this->config = array_merge($defaults, $config);
        $this->apiKey = $this->config['api_key'];
        $this->apiBase = rtrim($this->config['api_base'], '/');

        $this->httpClient = $this->config['http_client'] ?? new GuzzleClient([
            'base_uri' => $this->apiBase,
            'timeout' => $this->config['timeout'],
            'connect_timeout' => $this->config['connect_timeout'],
            'http_errors' => false,
            'headers' => [
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
                'User-Agent' => 'emailit-php/' . self::SDK_VERSION,
            ],
        ]);
    }

    public function getApiKey(): string
    {
        return $this->apiKey;
    }

    public function getApiBase(): string
    {
        return $this->apiBase;
    }

    /**
     * @throws ApiErrorException
     * @throws ApiConnectionException
     */
    public function request(string $method, string $path, ?array $params = null): ApiResponse
    {
        $options = [];

        if ($params !== null) {
            if (strtoupper($method) === 'GET') {
                $options['query'] = $params;
            } else {
                $options['json'] = $params;
            }
        }

        try {
            $response = $this->httpClient->request($method, $path, $options);
        } catch (ConnectException $e) {
            throw new ApiConnectionException(
                'Could not connect to the Emailit API: ' . $e->getMessage(),
                previous: $e,
            );
        } catch (RequestException $e) {
            if ($e->hasResponse()) {
                $response = $e->getResponse();
            } else {
                throw new ApiConnectionException(
                    'Communication with Emailit API failed: ' . $e->getMessage(),
                    previous: $e,
                );
            }
        }

        $statusCode = $response->getStatusCode();
        $headers = $response->getHeaders();
        $body = (string) $response->getBody();

        $apiResponse = new ApiResponse($statusCode, $headers, $body);

        if ($statusCode >= 400) {
            $this->handleErrorResponse($apiResponse);
        }

        return $apiResponse;
    }

    /**
     * @throws ApiErrorException
     */
    private function handleErrorResponse(ApiResponse $response): never
    {
        $message = $this->extractErrorMessage($response);

        throw ApiErrorException::factory(
            $message,
            $response->statusCode,
            $response->body,
            $response->json,
            $response->headers,
        );
    }

    private function extractErrorMessage(ApiResponse $response): string
    {
        if ($response->json !== null) {
            if (isset($response->json['error']) && is_string($response->json['error'])) {
                $msg = $response->json['error'];
                if (isset($response->json['message'])) {
                    $msg .= ': ' . $response->json['message'];
                }
                return $msg;
            }

            if (isset($response->json['error']['message'])) {
                return $response->json['error']['message'];
            }
        }

        return "API request failed with status {$response->statusCode}";
    }
}
