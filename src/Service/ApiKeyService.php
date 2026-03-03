<?php

namespace Emailit\Service;

use Emailit\ApiKey;
use Emailit\Collection;
use Emailit\EmailitObject;

class ApiKeyService extends AbstractService
{
    /**
     * Create an API key.
     *
     * @param array{name: string, scope?: string, sending_domain_id?: string} $params
     */
    public function create(array $params): ApiKey|EmailitObject
    {
        return $this->request('POST', '/v2/api-keys', $params);
    }

    /**
     * Retrieve an API key by ID.
     */
    public function get(string $id): ApiKey|EmailitObject
    {
        return $this->request('GET', $this->buildPath('/v2/api-keys/%s', $id));
    }

    /**
     * List all API keys.
     *
     * @param array{page?: int, limit?: int} $params
     */
    public function list(array $params = []): Collection
    {
        return $this->requestCollection('GET', '/v2/api-keys', $params ?: null);
    }

    /**
     * Update an API key.
     *
     * @param array{name?: string} $params
     */
    public function update(string $id, array $params): ApiKey|EmailitObject
    {
        return $this->request('POST', $this->buildPath('/v2/api-keys/%s', $id), $params);
    }

    /**
     * Delete an API key.
     */
    public function delete(string $id): ApiKey|EmailitObject
    {
        return $this->request('DELETE', $this->buildPath('/v2/api-keys/%s', $id));
    }
}
