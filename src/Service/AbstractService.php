<?php

namespace Emailit\Service;

use Emailit\ApiResponse;
use Emailit\BaseEmailitClient;
use Emailit\Collection;
use Emailit\EmailitObject;
use Emailit\Util\Util;

abstract class AbstractService
{
    private BaseEmailitClient $client;

    public function __construct(BaseEmailitClient $client)
    {
        $this->client = $client;
    }

    protected function getClient(): BaseEmailitClient
    {
        return $this->client;
    }

    protected function request(string $method, string $path, ?array $params = null): EmailitObject
    {
        $response = $this->client->request($method, $path, $params);
        $obj = Util::convertToEmailitObject($response->json);

        if ($obj !== null) {
            $obj->setLastResponse($response);
        }

        return $obj ?? new EmailitObject();
    }

    protected function requestCollection(string $method, string $path, ?array $params = null): Collection
    {
        $response = $this->client->request($method, $path, $params);
        $obj = Util::convertToEmailitObject($response->json);

        if ($obj instanceof Collection) {
            $obj->setLastResponse($response);
            return $obj;
        }

        $collection = new Collection($response->json ?? []);
        $collection->setLastResponse($response);

        return $collection;
    }

    protected function requestRaw(string $method, string $path, ?array $params = null): ApiResponse
    {
        return $this->client->request($method, $path, $params);
    }

    protected function buildPath(string $pattern, string ...$args): string
    {
        return sprintf($pattern, ...array_map('urlencode', $args));
    }
}
