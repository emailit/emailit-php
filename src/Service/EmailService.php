<?php

namespace Emailit\Service;

use Emailit\Collection;
use Emailit\Email;
use Emailit\EmailitObject;

class EmailService extends AbstractService
{
    /**
     * Send an email.
     *
     * @param array{
     *     from: string,
     *     to: string|string[],
     *     subject?: string,
     *     html?: string,
     *     text?: string,
     *     template?: string,
     *     variables?: array<string, mixed>,
     *     cc?: string|string[],
     *     bcc?: string|string[],
     *     reply_to?: string,
     *     attachments?: array<array{filename: string, content: string, content_type?: string}>,
     *     tags?: string[],
     *     metadata?: array<string, mixed>,
     *     tracking?: array{loads?: bool, clicks?: bool},
     *     scheduled_at?: string,
     * } $params
     */
    public function send(array $params): Email|EmailitObject
    {
        return $this->request('POST', '/v2/emails', $params);
    }

    /**
     * List emails.
     *
     * @param array{page?: int, limit?: int} $params
     */
    public function list(array $params = []): Collection
    {
        return $this->requestCollection('GET', '/v2/emails', $params ?: null);
    }

    /**
     * Retrieve a single email by ID.
     */
    public function get(string $id): Email|EmailitObject
    {
        return $this->request('GET', $this->buildPath('/v2/emails/%s', $id));
    }

    /**
     * Get the raw MIME source of an email.
     */
    public function getRaw(string $id): Email|EmailitObject
    {
        return $this->request('GET', $this->buildPath('/v2/emails/%s/raw', $id));
    }

    /**
     * Get attachments for an email.
     */
    public function getAttachments(string $id): Collection
    {
        return $this->requestCollection('GET', $this->buildPath('/v2/emails/%s/attachments', $id));
    }

    /**
     * Get the text/html body of an email.
     */
    public function getBody(string $id): EmailitObject
    {
        return $this->request('GET', $this->buildPath('/v2/emails/%s/body', $id));
    }

    /**
     * Get email metadata (headers, attachment info without content).
     */
    public function getMeta(string $id): Email|EmailitObject
    {
        return $this->request('GET', $this->buildPath('/v2/emails/%s/meta', $id));
    }

    /**
     * Update a scheduled email.
     *
     * @param array{scheduled_at: string} $params
     */
    public function update(string $id, array $params): Email|EmailitObject
    {
        return $this->request('POST', $this->buildPath('/v2/emails/%s', $id), $params);
    }

    /**
     * Cancel a scheduled or pending email.
     */
    public function cancel(string $id): Email|EmailitObject
    {
        return $this->request('POST', $this->buildPath('/v2/emails/%s/cancel', $id));
    }

    /**
     * Retry a failed, errored, or held email.
     */
    public function retry(string $id): Email|EmailitObject
    {
        return $this->request('POST', $this->buildPath('/v2/emails/%s/retry', $id));
    }
}
