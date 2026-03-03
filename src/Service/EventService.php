<?php

namespace Emailit\Service;

use Emailit\Collection;
use Emailit\EmailitObject;
use Emailit\Event;

class EventService extends AbstractService
{
    /**
     * List events.
     *
     * @param array{page?: int, limit?: int, type?: string, include_data?: bool} $params
     */
    public function list(array $params = []): Collection
    {
        return $this->requestCollection('GET', '/v2/events', $params ?: null);
    }

    /**
     * Retrieve an event by ID.
     */
    public function get(string $id): Event|EmailitObject
    {
        return $this->request('GET', $this->buildPath('/v2/events/%s', $id));
    }
}
