<?php

use Emailit\Contact;
use Emailit\Collection;

// ──────────────────────────────────────────────────
// create()
// ──────────────────────────────────────────────────

test('create() returns a Contact resource', function () {
    $body = [
        'object' => 'contact',
        'id' => 'con_123',
        'email' => 'user@example.com',
        'first_name' => 'John',
        'last_name' => 'Doe',
        'unsubscribed' => false,
    ];

    ['client' => $client] = mockClient([jsonResponse(201, $body)]);

    $contact = $client->contacts->create([
        'email' => 'user@example.com',
        'first_name' => 'John',
        'last_name' => 'Doe',
    ]);

    expect($contact)->toBeInstanceOf(Contact::class)
        ->and($contact->id)->toBe('con_123')
        ->and($contact->email)->toBe('user@example.com')
        ->and($contact->first_name)->toBe('John')
        ->and($contact->unsubscribed)->toBeFalse();
});

// ──────────────────────────────────────────────────
// get()
// ──────────────────────────────────────────────────

test('get() returns a Contact resource', function () {
    $body = [
        'object' => 'contact',
        'id' => 'con_123',
        'email' => 'user@example.com',
        'audiences' => [['id' => 'aud_1', 'name' => 'Newsletter']],
    ];

    ['client' => $client] = mockClient([jsonResponse(200, $body)]);

    $contact = $client->contacts->get('con_123');

    expect($contact)->toBeInstanceOf(Contact::class)
        ->and($contact->id)->toBe('con_123')
        ->and($contact->audiences)->toBeArray();
});

// ──────────────────────────────────────────────────
// update()
// ──────────────────────────────────────────────────

test('update() returns a Contact resource', function () {
    $body = [
        'object' => 'contact',
        'id' => 'con_123',
        'email' => 'user@example.com',
        'first_name' => 'Jane',
    ];

    ['client' => $client, 'handler' => $handler] = mockClient([jsonResponse(200, $body)]);

    $contact = $client->contacts->update('con_123', ['first_name' => 'Jane']);

    expect($contact)->toBeInstanceOf(Contact::class)
        ->and($contact->first_name)->toBe('Jane');

    expect($handler->getLastRequest()->getMethod())->toBe('POST');
});

// ──────────────────────────────────────────────────
// list()
// ──────────────────────────────────────────────────

test('list() returns a Collection of contacts', function () {
    $body = [
        'data' => [
            ['object' => 'contact', 'id' => 'con_1', 'email' => 'a@a.com'],
            ['object' => 'contact', 'id' => 'con_2', 'email' => 'b@b.com'],
        ],
        'next_page_url' => null,
        'previous_page_url' => null,
    ];

    ['client' => $client] = mockClient([jsonResponse(200, $body)]);

    $collection = $client->contacts->list();

    expect($collection)->toBeInstanceOf(Collection::class)
        ->and($collection)->toHaveCount(2)
        ->and($collection->getData()[0])->toBeInstanceOf(Contact::class);
});

// ──────────────────────────────────────────────────
// delete()
// ──────────────────────────────────────────────────

test('delete() returns a Contact resource', function () {
    $body = [
        'object' => 'contact',
        'id' => 'con_123',
        'email' => 'user@example.com',
        'deleted' => true,
    ];

    ['client' => $client, 'handler' => $handler] = mockClient([jsonResponse(200, $body)]);

    $contact = $client->contacts->delete('con_123');

    expect($contact)->toBeInstanceOf(Contact::class)
        ->and($contact->deleted)->toBeTrue();

    expect($handler->getLastRequest()->getMethod())->toBe('DELETE');
});
