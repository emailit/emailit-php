<?php

namespace Emailit\Service;

use Emailit\Collection;
use Emailit\EmailitObject;
use Emailit\Subscriber;

class SubscriberService extends AbstractService
{
    /**
     * Add a subscriber to an audience.
     *
     * @param array{email: string, first_name?: string, last_name?: string, custom_fields?: array<string, mixed>} $params
     */
    public function create(string $audienceId, array $params): Subscriber|EmailitObject
    {
        return $this->request(
            'POST',
            $this->buildPath('/v2/audiences/%s/subscribers', $audienceId),
            $params,
        );
    }

    /**
     * Retrieve a subscriber from an audience.
     */
    public function get(string $audienceId, string $subscriberId): Subscriber|EmailitObject
    {
        return $this->request(
            'GET',
            $this->buildPath('/v2/audiences/%s/subscribers/%s', $audienceId, $subscriberId),
        );
    }

    /**
     * Update a subscriber in an audience.
     *
     * @param array{email?: string, first_name?: string, last_name?: string, custom_fields?: array<string, mixed>, subscribed?: bool} $params
     */
    public function update(string $audienceId, string $subscriberId, array $params): Subscriber|EmailitObject
    {
        return $this->request(
            'POST',
            $this->buildPath('/v2/audiences/%s/subscribers/%s', $audienceId, $subscriberId),
            $params,
        );
    }

    /**
     * List subscribers in an audience.
     *
     * @param array{page?: int, limit?: int, subscribed?: bool} $params
     */
    public function list(string $audienceId, array $params = []): Collection
    {
        return $this->requestCollection(
            'GET',
            $this->buildPath('/v2/audiences/%s/subscribers', $audienceId),
            $params ?: null,
        );
    }

    /**
     * Delete a subscriber from an audience.
     */
    public function delete(string $audienceId, string $subscriberId): Subscriber|EmailitObject
    {
        return $this->request(
            'DELETE',
            $this->buildPath('/v2/audiences/%s/subscribers/%s', $audienceId, $subscriberId),
        );
    }
}
