<?php

namespace Emailit\Service;

use Emailit\Audience;
use Emailit\Collection;
use Emailit\EmailitObject;

class AudienceService extends AbstractService
{
    /**
     * Create an audience.
     *
     * @param array{name: string} $params
     */
    public function create(array $params): Audience|EmailitObject
    {
        return $this->request('POST', '/v2/audiences', $params);
    }

    /**
     * Retrieve an audience by ID.
     */
    public function get(string $id): Audience|EmailitObject
    {
        return $this->request('GET', $this->buildPath('/v2/audiences/%s', $id));
    }

    /**
     * Update an audience.
     *
     * @param array{name?: string} $params
     */
    public function update(string $id, array $params): Audience|EmailitObject
    {
        return $this->request('POST', $this->buildPath('/v2/audiences/%s', $id), $params);
    }

    /**
     * List all audiences.
     *
     * @param array{page?: int, limit?: int} $params
     */
    public function list(array $params = []): Collection
    {
        return $this->requestCollection('GET', '/v2/audiences', $params ?: null);
    }

    /**
     * Delete an audience.
     */
    public function delete(string $id): Audience|EmailitObject
    {
        return $this->request('DELETE', $this->buildPath('/v2/audiences/%s', $id));
    }
}
