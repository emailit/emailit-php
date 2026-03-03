<?php

use Emailit\EmailitObject;
use Emailit\Collection;

// ──────────────────────────────────────────────────
// create()
// ──────────────────────────────────────────────────

test('create() returns a template object', function () {
    $body = [
        'data' => [
            'id' => 'tem_123',
            'name' => 'Welcome',
            'subject' => 'Welcome!',
            'html' => '<h1>Hi</h1>',
        ],
        'message' => 'Template created.',
    ];

    ['client' => $client, 'handler' => $handler] = mockClient([jsonResponse(201, $body)]);

    $result = $client->templates->create([
        'name' => 'Welcome',
        'subject' => 'Welcome!',
        'html' => '<h1>Hi</h1>',
    ]);

    expect($result)->toBeInstanceOf(EmailitObject::class)
        ->and($result->message)->toBe('Template created.');

    $lastRequest = $handler->getLastRequest();
    expect($lastRequest->getMethod())->toBe('POST')
        ->and((string) $lastRequest->getUri())->toContain('/v2/templates');
});

// ──────────────────────────────────────────────────
// get()
// ──────────────────────────────────────────────────

test('get() returns a template object', function () {
    $body = [
        'data' => [
            'id' => 'tem_123',
            'name' => 'Welcome',
            'subject' => 'Welcome!',
            'versions' => [['id' => 'v1']],
        ],
    ];

    ['client' => $client] = mockClient([jsonResponse(200, $body)]);

    $result = $client->templates->get('tem_123');

    expect($result)->toBeInstanceOf(EmailitObject::class);
});

// ──────────────────────────────────────────────────
// update()
// ──────────────────────────────────────────────────

test('update() returns a template object', function () {
    $body = [
        'data' => [
            'id' => 'tem_123',
            'name' => 'Updated',
        ],
        'message' => 'Template updated.',
    ];

    ['client' => $client, 'handler' => $handler] = mockClient([jsonResponse(200, $body)]);

    $result = $client->templates->update('tem_123', ['name' => 'Updated']);

    expect($result)->toBeInstanceOf(EmailitObject::class)
        ->and($result->message)->toBe('Template updated.');

    expect($handler->getLastRequest()->getMethod())->toBe('POST');
});

// ──────────────────────────────────────────────────
// list()
// ──────────────────────────────────────────────────

test('list() returns a Collection of templates', function () {
    $body = [
        'data' => [
            ['id' => 'tem_1', 'name' => 'Template A'],
            ['id' => 'tem_2', 'name' => 'Template B'],
        ],
        'total_records' => 2,
        'per_page' => 20,
        'current_page' => 1,
        'total_pages' => 1,
    ];

    ['client' => $client] = mockClient([jsonResponse(200, $body)]);

    $collection = $client->templates->list();

    expect($collection)->toBeInstanceOf(Collection::class)
        ->and($collection)->toHaveCount(2);
});

// ──────────────────────────────────────────────────
// delete()
// ──────────────────────────────────────────────────

test('delete() returns a response object', function () {
    $body = [
        'data' => null,
        'message' => 'Template deleted.',
    ];

    ['client' => $client, 'handler' => $handler] = mockClient([jsonResponse(200, $body)]);

    $result = $client->templates->delete('tem_123');

    expect($result)->toBeInstanceOf(EmailitObject::class)
        ->and($result->message)->toBe('Template deleted.');

    expect($handler->getLastRequest()->getMethod())->toBe('DELETE');
});

// ──────────────────────────────────────────────────
// publish()
// ──────────────────────────────────────────────────

test('publish() returns a template object', function () {
    $body = [
        'data' => [
            'id' => 'tem_123',
            'name' => 'Welcome',
            'published_at' => '2026-01-01T00:00:00Z',
        ],
        'message' => 'Template published.',
    ];

    ['client' => $client, 'handler' => $handler] = mockClient([jsonResponse(200, $body)]);

    $result = $client->templates->publish('tem_123');

    expect($result)->toBeInstanceOf(EmailitObject::class)
        ->and($result->message)->toBe('Template published.');

    $lastRequest = $handler->getLastRequest();
    expect($lastRequest->getMethod())->toBe('POST')
        ->and((string) $lastRequest->getUri())->toContain('/v2/templates/tem_123/publish');
});
