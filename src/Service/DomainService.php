<?php

namespace Emailit\Service;

use Emailit\Collection;
use Emailit\Domain;
use Emailit\EmailitObject;

class DomainService extends AbstractService
{
    /**
     * Create a domain.
     *
     * @param array{name: string, track_loads?: bool, track_clicks?: bool} $params
     */
    public function create(array $params): Domain|EmailitObject
    {
        return $this->request('POST', '/v2/domains', $params);
    }

    /**
     * Retrieve a domain by ID.
     */
    public function get(string $id): Domain|EmailitObject
    {
        return $this->request('GET', $this->buildPath('/v2/domains/%s', $id));
    }

    /**
     * Verify a domain's DNS records.
     */
    public function verify(string $id): Domain|EmailitObject
    {
        return $this->request('POST', $this->buildPath('/v2/domains/%s/verify', $id));
    }

    /**
     * Update a domain.
     *
     * @param array{track_loads?: bool, track_clicks?: bool} $params
     */
    public function update(string $id, array $params): Domain|EmailitObject
    {
        return $this->request('PATCH', $this->buildPath('/v2/domains/%s', $id), $params);
    }

    /**
     * List all domains.
     *
     * @param array{page?: int, limit?: int} $params
     */
    public function list(array $params = []): Collection
    {
        return $this->requestCollection('GET', '/v2/domains', $params ?: null);
    }

    /**
     * Delete a domain.
     */
    public function delete(string $id): Domain|EmailitObject
    {
        return $this->request('DELETE', $this->buildPath('/v2/domains/%s', $id));
    }
}
