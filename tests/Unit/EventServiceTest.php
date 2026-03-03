<?php

use Emailit\Event;
use Emailit\Collection;

// ──────────────────────────────────────────────────
// list()
// ──────────────────────────────────────────────────

test('list() returns a Collection of events', function () {
    $body = [
        'data' => [
            ['object' => 'event', 'id' => 'evt_1', 'type' => 'email.sent'],
            ['object' => 'event', 'id' => 'evt_2', 'type' => 'email.delivered'],
        ],
        'next_page_url' => null,
        'previous_page_url' => null,
    ];

    ['client' => $client] = mockClient([jsonResponse(200, $body)]);

    $collection = $client->events()->list();

    expect($collection)->toBeInstanceOf(Collection::class)
        ->and($collection)->toHaveCount(2)
        ->and($collection->getData()[0])->toBeInstanceOf(Event::class)
        ->and($collection->getData()[0]->type)->toBe('email.sent');
});

test('list() passes filter params', function () {
    $body = [
        'data' => [
            ['object' => 'event', 'id' => 'evt_1', 'type' => 'email.bounced'],
        ],
        'next_page_url' => null,
        'previous_page_url' => null,
    ];

    ['client' => $client, 'handler' => $handler] = mockClient([jsonResponse(200, $body)]);

    $collection = $client->events()->list(['type' => 'email.bounced', 'limit' => 10]);

    expect($collection)->toHaveCount(1);

    $lastRequest = $handler->getLastRequest();
    $query = $lastRequest->getUri()->getQuery();
    expect($query)->toContain('type=email.bounced')
        ->and($query)->toContain('limit=10');
});

// ──────────────────────────────────────────────────
// get()
// ──────────────────────────────────────────────────

test('get() returns an Event resource', function () {
    $body = [
        'object' => 'event',
        'id' => 'evt_123',
        'type' => 'email.delivered',
        'data' => ['email_id' => 'em_456'],
        'created_at' => '2026-01-01T00:00:00Z',
    ];

    ['client' => $client] = mockClient([jsonResponse(200, $body)]);

    $event = $client->events()->get('evt_123');

    expect($event)->toBeInstanceOf(Event::class)
        ->and($event->id)->toBe('evt_123')
        ->and($event->type)->toBe('email.delivered')
        ->and($event->data)->toBeArray()
        ->and($event->data['email_id'])->toBe('em_456');
});
