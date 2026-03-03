<?php

namespace Emailit\Service;

use Emailit\ApiResponse;
use Emailit\Collection;
use Emailit\EmailitObject;
use Emailit\EmailVerificationList;

class EmailVerificationListService extends AbstractService
{
    /**
     * Create an email verification list.
     *
     * @param array{name: string, emails: string[]} $params
     */
    public function create(array $params): EmailVerificationList|EmailitObject
    {
        return $this->request('POST', '/v2/email-verification-lists', $params);
    }

    /**
     * List all email verification lists.
     *
     * @param array{page?: int, limit?: int} $params
     */
    public function list(array $params = []): Collection
    {
        return $this->requestCollection('GET', '/v2/email-verification-lists', $params ?: null);
    }

    /**
     * Retrieve an email verification list by ID.
     */
    public function get(string $id): EmailVerificationList|EmailitObject
    {
        return $this->request('GET', $this->buildPath('/v2/email-verification-lists/%s', $id));
    }

    /**
     * Get verification results for a list.
     *
     * @param array{page?: int, limit?: int} $params
     */
    public function results(string $id, array $params = []): Collection
    {
        return $this->requestCollection(
            'GET',
            $this->buildPath('/v2/email-verification-lists/%s/results', $id),
            $params ?: null,
        );
    }

    /**
     * Export verification results as an XLSX file.
     *
     * Returns the raw API response. Access the binary content via $response->body.
     */
    public function export(string $id): ApiResponse
    {
        return $this->requestRaw(
            'GET',
            $this->buildPath('/v2/email-verification-lists/%s/export', $id),
        );
    }
}
