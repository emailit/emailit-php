<?php

namespace Emailit\Service;

use Emailit\Collection;
use Emailit\EmailitObject;
use Emailit\Webhook;

class WebhookService extends AbstractService
{
    /**
     * Create a webhook.
     *
     * @param array{name: string, url: string, all_events?: bool, enabled?: bool, events?: string[]} $params
     */
    public function create(array $params): Webhook|EmailitObject
    {
        return $this->request('POST', '/v2/webhooks', $params);
    }

    /**
     * Retrieve a webhook by ID.
     */
    public function get(string $id): Webhook|EmailitObject
    {
        return $this->request('GET', $this->buildPath('/v2/webhooks/%s', $id));
    }

    /**
     * Update a webhook.
     *
     * @param array{name?: string, url?: string, enabled?: bool, events?: string[]} $params
     */
    public function update(string $id, array $params): Webhook|EmailitObject
    {
        return $this->request('POST', $this->buildPath('/v2/webhooks/%s', $id), $params);
    }

    /**
     * List all webhooks.
     *
     * @param array{page?: int, limit?: int} $params
     */
    public function list(array $params = []): Collection
    {
        return $this->requestCollection('GET', '/v2/webhooks', $params ?: null);
    }

    /**
     * Delete a webhook.
     */
    public function delete(string $id): Webhook|EmailitObject
    {
        return $this->request('DELETE', $this->buildPath('/v2/webhooks/%s', $id));
    }
}
