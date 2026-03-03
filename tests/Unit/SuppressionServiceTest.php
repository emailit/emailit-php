<?php

use Emailit\Suppression;
use Emailit\Collection;

// ──────────────────────────────────────────────────
// create()
// ──────────────────────────────────────────────────

test('create() returns a Suppression resource', function () {
    $body = [
        'object' => 'suppression',
        'id' => 'sup_123',
        'email' => 'spam@example.com',
        'type' => 'hard_bounce',
        'reason' => 'Manual suppression',
    ];

    ['client' => $client] = mockClient([jsonResponse(201, $body)]);

    $suppression = $client->suppressions->create([
        'email' => 'spam@example.com',
        'type' => 'hard_bounce',
        'reason' => 'Manual suppression',
    ]);

    expect($suppression)->toBeInstanceOf(Suppression::class)
        ->and($suppression->id)->toBe('sup_123')
        ->and($suppression->email)->toBe('spam@example.com')
        ->and($suppression->type)->toBe('hard_bounce');
});

// ──────────────────────────────────────────────────
// get()
// ──────────────────────────────────────────────────

test('get() returns a Suppression resource', function () {
    $body = [
        'object' => 'suppression',
        'id' => 'sup_123',
        'email' => 'spam@example.com',
        'type' => 'hard_bounce',
    ];

    ['client' => $client] = mockClient([jsonResponse(200, $body)]);

    $suppression = $client->suppressions->get('sup_123');

    expect($suppression)->toBeInstanceOf(Suppression::class)
        ->and($suppression->id)->toBe('sup_123');
});

// ──────────────────────────────────────────────────
// update()
// ──────────────────────────────────────────────────

test('update() returns a Suppression resource', function () {
    $body = [
        'object' => 'suppression',
        'id' => 'sup_123',
        'email' => 'spam@example.com',
        'reason' => 'Updated reason',
    ];

    ['client' => $client, 'handler' => $handler] = mockClient([jsonResponse(200, $body)]);

    $suppression = $client->suppressions->update('sup_123', ['reason' => 'Updated reason']);

    expect($suppression)->toBeInstanceOf(Suppression::class)
        ->and($suppression->reason)->toBe('Updated reason');

    expect($handler->getLastRequest()->getMethod())->toBe('POST');
});

// ──────────────────────────────────────────────────
// list()
// ──────────────────────────────────────────────────

test('list() returns a Collection of suppressions', function () {
    $body = [
        'data' => [
            ['object' => 'suppression', 'id' => 'sup_1', 'email' => 'a@a.com'],
            ['object' => 'suppression', 'id' => 'sup_2', 'email' => 'b@b.com'],
        ],
        'next_page_url' => null,
        'previous_page_url' => null,
    ];

    ['client' => $client] = mockClient([jsonResponse(200, $body)]);

    $collection = $client->suppressions->list();

    expect($collection)->toBeInstanceOf(Collection::class)
        ->and($collection)->toHaveCount(2)
        ->and($collection->getData()[0])->toBeInstanceOf(Suppression::class);
});

// ──────────────────────────────────────────────────
// delete()
// ──────────────────────────────────────────────────

test('delete() returns a Suppression resource', function () {
    $body = [
        'object' => 'suppression',
        'id' => 'sup_123',
        'email' => 'spam@example.com',
        'deleted' => true,
    ];

    ['client' => $client, 'handler' => $handler] = mockClient([jsonResponse(200, $body)]);

    $suppression = $client->suppressions->delete('sup_123');

    expect($suppression)->toBeInstanceOf(Suppression::class)
        ->and($suppression->deleted)->toBeTrue();

    expect($handler->getLastRequest()->getMethod())->toBe('DELETE');
});
