<?php

namespace Emailit\Service;

use Emailit\Collection;
use Emailit\EmailitObject;
use Emailit\Template;

class TemplateService extends AbstractService
{
    /**
     * Create a template.
     *
     * @param array{name: string, alias?: string, from?: string, subject?: string, reply_to?: string, html?: string, text?: string, editor?: string} $params
     */
    public function create(array $params): Template|EmailitObject
    {
        return $this->request('POST', '/v2/templates', $params);
    }

    /**
     * Retrieve a template by ID.
     */
    public function get(string $id): Template|EmailitObject
    {
        return $this->request('GET', $this->buildPath('/v2/templates/%s', $id));
    }

    /**
     * Update a template.
     *
     * @param array{name?: string, subject?: string, alias?: string, from?: string, reply_to?: string, html?: string, text?: string, editor?: string} $params
     */
    public function update(string $id, array $params): Template|EmailitObject
    {
        return $this->request('POST', $this->buildPath('/v2/templates/%s', $id), $params);
    }

    /**
     * List all templates.
     *
     * @param array{page?: int, per_page?: int} $params
     */
    public function list(array $params = []): Collection
    {
        return $this->requestCollection('GET', '/v2/templates', $params ?: null);
    }

    /**
     * Delete a template.
     */
    public function delete(string $id): EmailitObject
    {
        return $this->request('DELETE', $this->buildPath('/v2/templates/%s', $id));
    }

    /**
     * Publish a template version.
     */
    public function publish(string $id): Template|EmailitObject
    {
        return $this->request('POST', $this->buildPath('/v2/templates/%s/publish', $id));
    }
}
