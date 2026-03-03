<?php

namespace Emailit\Service;

use Emailit\Collection;
use Emailit\EmailitObject;
use Emailit\Suppression;

class SuppressionService extends AbstractService
{
    /**
     * Create a suppression.
     *
     * @param array{email: string, type?: string, reason?: string, keep_until?: string} $params
     */
    public function create(array $params): Suppression|EmailitObject
    {
        return $this->request('POST', '/v2/suppressions', $params);
    }

    /**
     * Retrieve a suppression by ID or email.
     */
    public function get(string $id): Suppression|EmailitObject
    {
        return $this->request('GET', $this->buildPath('/v2/suppressions/%s', $id));
    }

    /**
     * Update a suppression.
     *
     * @param array{reason?: string, keep_until?: string, email?: string, type?: string} $params
     */
    public function update(string $id, array $params): Suppression|EmailitObject
    {
        return $this->request('POST', $this->buildPath('/v2/suppressions/%s', $id), $params);
    }

    /**
     * List all suppressions.
     *
     * @param array{page?: int, limit?: int} $params
     */
    public function list(array $params = []): Collection
    {
        return $this->requestCollection('GET', '/v2/suppressions', $params ?: null);
    }

    /**
     * Delete a suppression by ID or email.
     */
    public function delete(string $id): Suppression|EmailitObject
    {
        return $this->request('DELETE', $this->buildPath('/v2/suppressions/%s', $id));
    }
}
