<?php

use Emailit\ApiKey;
use Emailit\Collection;
use Emailit\Exceptions\AuthenticationException;

// ──────────────────────────────────────────────────
// create()
// ──────────────────────────────────────────────────

test('create() returns an ApiKey resource with key', function () {
    $body = [
        'object' => 'api_key',
        'id' => 'ak_123',
        'name' => 'My Key',
        'scope' => 'full',
        'key' => 'em_live_abc123',
    ];

    ['client' => $client] = mockClient([jsonResponse(201, $body)]);

    $apiKey = $client->apiKeys()->create(['name' => 'My Key', 'scope' => 'full']);

    expect($apiKey)->toBeInstanceOf(ApiKey::class)
        ->and($apiKey->id)->toBe('ak_123')
        ->and($apiKey->name)->toBe('My Key')
        ->and($apiKey->key)->toBe('em_live_abc123');
});

// ──────────────────────────────────────────────────
// get()
// ──────────────────────────────────────────────────

test('get() returns an ApiKey resource', function () {
    $body = [
        'object' => 'api_key',
        'id' => 'ak_123',
        'name' => 'My Key',
        'scope' => 'full',
    ];

    ['client' => $client] = mockClient([jsonResponse(200, $body)]);

    $apiKey = $client->apiKeys()->get('ak_123');

    expect($apiKey)->toBeInstanceOf(ApiKey::class)
        ->and($apiKey->id)->toBe('ak_123');
});

// ──────────────────────────────────────────────────
// list()
// ──────────────────────────────────────────────────

test('list() returns a Collection of API keys', function () {
    $body = [
        'data' => [
            ['object' => 'api_key', 'id' => 'ak_1', 'name' => 'Key 1'],
            ['object' => 'api_key', 'id' => 'ak_2', 'name' => 'Key 2'],
        ],
        'next_page_url' => null,
        'previous_page_url' => null,
    ];

    ['client' => $client] = mockClient([jsonResponse(200, $body)]);

    $collection = $client->apiKeys()->list();

    expect($collection)->toBeInstanceOf(Collection::class)
        ->and($collection)->toHaveCount(2)
        ->and($collection->getData()[0])->toBeInstanceOf(ApiKey::class);
});

// ──────────────────────────────────────────────────
// update()
// ──────────────────────────────────────────────────

test('update() returns an ApiKey resource', function () {
    $body = [
        'object' => 'api_key',
        'id' => 'ak_123',
        'name' => 'Updated Key',
    ];

    ['client' => $client, 'handler' => $handler] = mockClient([jsonResponse(200, $body)]);

    $apiKey = $client->apiKeys()->update('ak_123', ['name' => 'Updated Key']);

    expect($apiKey)->toBeInstanceOf(ApiKey::class)
        ->and($apiKey->name)->toBe('Updated Key');

    $lastRequest = $handler->getLastRequest();
    expect($lastRequest->getMethod())->toBe('POST');
});

// ──────────────────────────────────────────────────
// delete()
// ──────────────────────────────────────────────────

test('delete() returns an ApiKey resource', function () {
    $body = [
        'object' => 'api_key',
        'id' => 'ak_123',
        'name' => 'My Key',
        'deleted' => true,
    ];

    ['client' => $client, 'handler' => $handler] = mockClient([jsonResponse(200, $body)]);

    $apiKey = $client->apiKeys()->delete('ak_123');

    expect($apiKey)->toBeInstanceOf(ApiKey::class)
        ->and($apiKey->deleted)->toBeTrue();

    expect($handler->getLastRequest()->getMethod())->toBe('DELETE');
});

test('apiKeys service throws AuthenticationException on 401', function () {
    ['client' => $client] = mockClient([
        jsonResponse(401, ['error' => 'Unauthenticated']),
    ]);

    $client->apiKeys()->list();
})->throws(AuthenticationException::class);
