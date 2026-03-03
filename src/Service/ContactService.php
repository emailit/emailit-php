<?php

namespace Emailit\Service;

use Emailit\Collection;
use Emailit\Contact;
use Emailit\EmailitObject;

class ContactService extends AbstractService
{
    /**
     * Create a contact.
     *
     * @param array{email: string, first_name?: string, last_name?: string, custom_fields?: array<string, mixed>, audiences?: string[], unsubscribed?: bool} $params
     */
    public function create(array $params): Contact|EmailitObject
    {
        return $this->request('POST', '/v2/contacts', $params);
    }

    /**
     * Retrieve a contact by ID.
     */
    public function get(string $id): Contact|EmailitObject
    {
        return $this->request('GET', $this->buildPath('/v2/contacts/%s', $id));
    }

    /**
     * Update a contact.
     *
     * @param array{first_name?: string, last_name?: string, custom_fields?: array<string, mixed>, email?: string, audiences?: string[], unsubscribed?: bool} $params
     */
    public function update(string $id, array $params): Contact|EmailitObject
    {
        return $this->request('POST', $this->buildPath('/v2/contacts/%s', $id), $params);
    }

    /**
     * List all contacts.
     *
     * @param array{page?: int, limit?: int} $params
     */
    public function list(array $params = []): Collection
    {
        return $this->requestCollection('GET', '/v2/contacts', $params ?: null);
    }

    /**
     * Delete a contact.
     */
    public function delete(string $id): Contact|EmailitObject
    {
        return $this->request('DELETE', $this->buildPath('/v2/contacts/%s', $id));
    }
}
