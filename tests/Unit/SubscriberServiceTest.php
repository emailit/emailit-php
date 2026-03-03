<?php

use Emailit\Subscriber;
use Emailit\Collection;

// ──────────────────────────────────────────────────
// create()
// ──────────────────────────────────────────────────

test('create() adds a subscriber to an audience', function () {
    $body = [
        'object' => 'subscriber',
        'id' => 'sub_123',
        'audience_id' => 'aud_456',
        'email' => 'user@example.com',
        'first_name' => 'John',
        'last_name' => 'Doe',
        'subscribed' => true,
    ];

    ['client' => $client, 'handler' => $handler] = mockClient([jsonResponse(201, $body)]);

    $subscriber = $client->subscribers->create('aud_456', [
        'email' => 'user@example.com',
        'first_name' => 'John',
        'last_name' => 'Doe',
    ]);

    expect($subscriber)->toBeInstanceOf(Subscriber::class)
        ->and($subscriber->id)->toBe('sub_123')
        ->and($subscriber->email)->toBe('user@example.com')
        ->and($subscriber->audience_id)->toBe('aud_456')
        ->and($subscriber->subscribed)->toBeTrue();

    $lastRequest = $handler->getLastRequest();
    expect($lastRequest->getMethod())->toBe('POST')
        ->and((string) $lastRequest->getUri())->toContain('/v2/audiences/aud_456/subscribers');
});

// ──────────────────────────────────────────────────
// get()
// ──────────────────────────────────────────────────

test('get() returns a Subscriber resource', function () {
    $body = [
        'object' => 'subscriber',
        'id' => 'sub_123',
        'audience_id' => 'aud_456',
        'email' => 'user@example.com',
    ];

    ['client' => $client, 'handler' => $handler] = mockClient([jsonResponse(200, $body)]);

    $subscriber = $client->subscribers->get('aud_456', 'sub_123');

    expect($subscriber)->toBeInstanceOf(Subscriber::class)
        ->and($subscriber->id)->toBe('sub_123');

    $lastRequest = $handler->getLastRequest();
    expect((string) $lastRequest->getUri())->toContain('/v2/audiences/aud_456/subscribers/sub_123');
});

// ──────────────────────────────────────────────────
// update()
// ──────────────────────────────────────────────────

test('update() returns a Subscriber resource', function () {
    $body = [
        'object' => 'subscriber',
        'id' => 'sub_123',
        'audience_id' => 'aud_456',
        'email' => 'user@example.com',
        'first_name' => 'Jane',
    ];

    ['client' => $client, 'handler' => $handler] = mockClient([jsonResponse(200, $body)]);

    $subscriber = $client->subscribers->update('aud_456', 'sub_123', ['first_name' => 'Jane']);

    expect($subscriber)->toBeInstanceOf(Subscriber::class)
        ->and($subscriber->first_name)->toBe('Jane');

    $lastRequest = $handler->getLastRequest();
    expect($lastRequest->getMethod())->toBe('POST')
        ->and((string) $lastRequest->getUri())->toContain('/v2/audiences/aud_456/subscribers/sub_123');
});

// ──────────────────────────────────────────────────
// list()
// ──────────────────────────────────────────────────

test('list() returns a Collection of subscribers', function () {
    $body = [
        'data' => [
            ['object' => 'subscriber', 'id' => 'sub_1', 'email' => 'a@a.com'],
            ['object' => 'subscriber', 'id' => 'sub_2', 'email' => 'b@b.com'],
        ],
        'next_page_url' => 'https://api.emailit.com/v2/audiences/aud_456/subscribers?page=2',
        'previous_page_url' => null,
    ];

    ['client' => $client, 'handler' => $handler] = mockClient([jsonResponse(200, $body)]);

    $collection = $client->subscribers->list('aud_456');

    expect($collection)->toBeInstanceOf(Collection::class)
        ->and($collection)->toHaveCount(2)
        ->and($collection->hasMore())->toBeTrue()
        ->and($collection->getData()[0])->toBeInstanceOf(Subscriber::class);

    $lastRequest = $handler->getLastRequest();
    expect((string) $lastRequest->getUri())->toContain('/v2/audiences/aud_456/subscribers');
});

// ──────────────────────────────────────────────────
// delete()
// ──────────────────────────────────────────────────

test('delete() returns a Subscriber resource', function () {
    $body = [
        'object' => 'subscriber',
        'id' => 'sub_123',
        'email' => 'user@example.com',
        'deleted' => true,
    ];

    ['client' => $client, 'handler' => $handler] = mockClient([jsonResponse(200, $body)]);

    $subscriber = $client->subscribers->delete('aud_456', 'sub_123');

    expect($subscriber)->toBeInstanceOf(Subscriber::class)
        ->and($subscriber->deleted)->toBeTrue();

    expect($handler->getLastRequest()->getMethod())->toBe('DELETE');
});
