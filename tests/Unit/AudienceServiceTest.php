<?php

use Emailit\Audience;
use Emailit\Collection;
use Emailit\Exceptions\InvalidRequestException;

// ──────────────────────────────────────────────────
// create()
// ──────────────────────────────────────────────────

test('create() returns an Audience resource', function () {
    $body = [
        'object' => 'audience',
        'id' => 'aud_123',
        'name' => 'Newsletter',
        'token' => 'abc123',
    ];

    ['client' => $client] = mockClient([jsonResponse(201, $body)]);

    $audience = $client->audiences()->create(['name' => 'Newsletter']);

    expect($audience)->toBeInstanceOf(Audience::class)
        ->and($audience->id)->toBe('aud_123')
        ->and($audience->name)->toBe('Newsletter')
        ->and($audience->token)->toBe('abc123');
});

// ──────────────────────────────────────────────────
// get()
// ──────────────────────────────────────────────────

test('get() returns an Audience resource', function () {
    $body = [
        'object' => 'audience',
        'id' => 'aud_123',
        'name' => 'Newsletter',
    ];

    ['client' => $client] = mockClient([jsonResponse(200, $body)]);

    $audience = $client->audiences()->get('aud_123');

    expect($audience)->toBeInstanceOf(Audience::class)
        ->and($audience->id)->toBe('aud_123');
});

// ──────────────────────────────────────────────────
// update()
// ──────────────────────────────────────────────────

test('update() returns an Audience resource', function () {
    $body = [
        'object' => 'audience',
        'id' => 'aud_123',
        'name' => 'Updated Newsletter',
    ];

    ['client' => $client, 'handler' => $handler] = mockClient([jsonResponse(200, $body)]);

    $audience = $client->audiences()->update('aud_123', ['name' => 'Updated Newsletter']);

    expect($audience)->toBeInstanceOf(Audience::class)
        ->and($audience->name)->toBe('Updated Newsletter');

    expect($handler->getLastRequest()->getMethod())->toBe('POST');
});

// ──────────────────────────────────────────────────
// list()
// ──────────────────────────────────────────────────

test('list() returns a Collection of audiences', function () {
    $body = [
        'data' => [
            ['object' => 'audience', 'id' => 'aud_1', 'name' => 'List A'],
            ['object' => 'audience', 'id' => 'aud_2', 'name' => 'List B'],
        ],
        'next_page_url' => null,
        'previous_page_url' => null,
    ];

    ['client' => $client] = mockClient([jsonResponse(200, $body)]);

    $collection = $client->audiences()->list();

    expect($collection)->toBeInstanceOf(Collection::class)
        ->and($collection)->toHaveCount(2)
        ->and($collection->getData()[0])->toBeInstanceOf(Audience::class);
});

// ──────────────────────────────────────────────────
// delete()
// ──────────────────────────────────────────────────

test('delete() returns an Audience resource', function () {
    $body = [
        'object' => 'audience',
        'id' => 'aud_123',
        'name' => 'Newsletter',
        'deleted' => true,
    ];

    ['client' => $client, 'handler' => $handler] = mockClient([jsonResponse(200, $body)]);

    $audience = $client->audiences()->delete('aud_123');

    expect($audience)->toBeInstanceOf(Audience::class)
        ->and($audience->deleted)->toBeTrue();

    expect($handler->getLastRequest()->getMethod())->toBe('DELETE');
});

test('audiences service throws InvalidRequestException on 404', function () {
    ['client' => $client] = mockClient([
        jsonResponse(404, ['error' => 'Not found']),
    ]);

    $client->audiences()->get('aud_nonexistent');
})->throws(InvalidRequestException::class);
