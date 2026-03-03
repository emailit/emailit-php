<?php

use Emailit\Webhook;
use Emailit\Collection;

// ──────────────────────────────────────────────────
// create()
// ──────────────────────────────────────────────────

test('create() returns a Webhook resource', function () {
    $body = [
        'object' => 'webhook',
        'id' => 'wh_123',
        'name' => 'My Webhook',
        'url' => 'https://example.com/hook',
        'all_events' => true,
        'enabled' => true,
        'events' => [],
    ];

    ['client' => $client] = mockClient([jsonResponse(201, $body)]);

    $webhook = $client->webhooks->create([
        'name' => 'My Webhook',
        'url' => 'https://example.com/hook',
        'all_events' => true,
    ]);

    expect($webhook)->toBeInstanceOf(Webhook::class)
        ->and($webhook->id)->toBe('wh_123')
        ->and($webhook->name)->toBe('My Webhook')
        ->and($webhook->url)->toBe('https://example.com/hook')
        ->and($webhook->all_events)->toBeTrue();
});

// ──────────────────────────────────────────────────
// get()
// ──────────────────────────────────────────────────

test('get() returns a Webhook resource', function () {
    $body = [
        'object' => 'webhook',
        'id' => 'wh_123',
        'name' => 'My Webhook',
        'url' => 'https://example.com/hook',
    ];

    ['client' => $client] = mockClient([jsonResponse(200, $body)]);

    $webhook = $client->webhooks->get('wh_123');

    expect($webhook)->toBeInstanceOf(Webhook::class)
        ->and($webhook->id)->toBe('wh_123');
});

// ──────────────────────────────────────────────────
// update()
// ──────────────────────────────────────────────────

test('update() returns a Webhook resource', function () {
    $body = [
        'object' => 'webhook',
        'id' => 'wh_123',
        'name' => 'Updated Webhook',
        'url' => 'https://example.com/hook-v2',
    ];

    ['client' => $client, 'handler' => $handler] = mockClient([jsonResponse(200, $body)]);

    $webhook = $client->webhooks->update('wh_123', [
        'name' => 'Updated Webhook',
        'url' => 'https://example.com/hook-v2',
    ]);

    expect($webhook)->toBeInstanceOf(Webhook::class)
        ->and($webhook->name)->toBe('Updated Webhook');

    expect($handler->getLastRequest()->getMethod())->toBe('POST');
});

// ──────────────────────────────────────────────────
// list()
// ──────────────────────────────────────────────────

test('list() returns a Collection of webhooks', function () {
    $body = [
        'data' => [
            ['object' => 'webhook', 'id' => 'wh_1', 'name' => 'Hook A'],
            ['object' => 'webhook', 'id' => 'wh_2', 'name' => 'Hook B'],
        ],
        'next_page_url' => null,
        'previous_page_url' => null,
    ];

    ['client' => $client] = mockClient([jsonResponse(200, $body)]);

    $collection = $client->webhooks->list();

    expect($collection)->toBeInstanceOf(Collection::class)
        ->and($collection)->toHaveCount(2)
        ->and($collection->getData()[0])->toBeInstanceOf(Webhook::class);
});

// ──────────────────────────────────────────────────
// delete()
// ──────────────────────────────────────────────────

test('delete() returns a Webhook resource', function () {
    $body = [
        'object' => 'webhook',
        'id' => 'wh_123',
        'name' => 'My Webhook',
        'deleted' => true,
    ];

    ['client' => $client, 'handler' => $handler] = mockClient([jsonResponse(200, $body)]);

    $webhook = $client->webhooks->delete('wh_123');

    expect($webhook)->toBeInstanceOf(Webhook::class)
        ->and($webhook->deleted)->toBeTrue();

    expect($handler->getLastRequest()->getMethod())->toBe('DELETE');
});
