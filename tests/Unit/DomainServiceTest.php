<?php

use Emailit\Domain;
use Emailit\Collection;
use Emailit\Exceptions\AuthenticationException;
use Emailit\Exceptions\InvalidRequestException;

// ──────────────────────────────────────────────────
// create()
// ──────────────────────────────────────────────────

test('create() returns a Domain resource', function () {
    $body = [
        'object' => 'domain',
        'id' => 'sd_123',
        'name' => 'example.com',
        'track_loads' => true,
        'track_clicks' => false,
    ];

    ['client' => $client] = mockClient([jsonResponse(201, $body)]);

    $domain = $client->domains->create(['name' => 'example.com', 'track_loads' => true]);

    expect($domain)->toBeInstanceOf(Domain::class)
        ->and($domain->id)->toBe('sd_123')
        ->and($domain->name)->toBe('example.com')
        ->and($domain->track_loads)->toBeTrue();
});

// ──────────────────────────────────────────────────
// get()
// ──────────────────────────────────────────────────

test('get() returns a Domain resource', function () {
    $body = [
        'object' => 'domain',
        'id' => 'sd_123',
        'name' => 'example.com',
        'spf_status' => 'verified',
        'dkim_status' => 'verified',
    ];

    ['client' => $client] = mockClient([jsonResponse(200, $body)]);

    $domain = $client->domains->get('sd_123');

    expect($domain)->toBeInstanceOf(Domain::class)
        ->and($domain->id)->toBe('sd_123')
        ->and($domain->spf_status)->toBe('verified');
});

// ──────────────────────────────────────────────────
// verify()
// ──────────────────────────────────────────────────

test('verify() returns a Domain resource', function () {
    $body = [
        'object' => 'domain',
        'id' => 'sd_123',
        'name' => 'example.com',
        'verified_at' => '2026-01-01T00:00:00Z',
    ];

    ['client' => $client, 'handler' => $handler] = mockClient([jsonResponse(200, $body)]);

    $domain = $client->domains->verify('sd_123');

    expect($domain)->toBeInstanceOf(Domain::class)
        ->and($domain->verified_at)->toBe('2026-01-01T00:00:00Z');

    $lastRequest = $handler->getLastRequest();
    expect($lastRequest->getMethod())->toBe('POST')
        ->and((string) $lastRequest->getUri())->toContain('/v2/domains/sd_123/verify');
});

// ──────────────────────────────────────────────────
// update()
// ──────────────────────────────────────────────────

test('update() sends PATCH and returns a Domain resource', function () {
    $body = [
        'object' => 'domain',
        'id' => 'sd_123',
        'name' => 'example.com',
        'track_clicks' => true,
    ];

    ['client' => $client, 'handler' => $handler] = mockClient([jsonResponse(200, $body)]);

    $domain = $client->domains->update('sd_123', ['track_clicks' => true]);

    expect($domain)->toBeInstanceOf(Domain::class)
        ->and($domain->track_clicks)->toBeTrue();

    $lastRequest = $handler->getLastRequest();
    expect($lastRequest->getMethod())->toBe('PATCH');
});

// ──────────────────────────────────────────────────
// list()
// ──────────────────────────────────────────────────

test('list() returns a Collection of domains', function () {
    $body = [
        'data' => [
            ['object' => 'domain', 'id' => 'sd_1', 'name' => 'a.com'],
            ['object' => 'domain', 'id' => 'sd_2', 'name' => 'b.com'],
        ],
        'next_page_url' => null,
        'previous_page_url' => null,
    ];

    ['client' => $client] = mockClient([jsonResponse(200, $body)]);

    $collection = $client->domains->list();

    expect($collection)->toBeInstanceOf(Collection::class)
        ->and($collection)->toHaveCount(2)
        ->and($collection->getData()[0])->toBeInstanceOf(Domain::class)
        ->and($collection->getData()[0]->name)->toBe('a.com');
});

// ──────────────────────────────────────────────────
// delete()
// ──────────────────────────────────────────────────

test('delete() returns a Domain resource', function () {
    $body = [
        'object' => 'domain',
        'id' => 'sd_123',
        'name' => 'example.com',
        'deleted' => true,
    ];

    ['client' => $client, 'handler' => $handler] = mockClient([jsonResponse(200, $body)]);

    $domain = $client->domains->delete('sd_123');

    expect($domain)->toBeInstanceOf(Domain::class)
        ->and($domain->deleted)->toBeTrue();

    $lastRequest = $handler->getLastRequest();
    expect($lastRequest->getMethod())->toBe('DELETE');
});

// ──────────────────────────────────────────────────
// Error handling
// ──────────────────────────────────────────────────

test('domains service throws AuthenticationException on 401', function () {
    ['client' => $client] = mockClient([
        jsonResponse(401, ['error' => 'Unauthenticated']),
    ]);

    $client->domains->list();
})->throws(AuthenticationException::class);

test('domains service throws InvalidRequestException on 404', function () {
    ['client' => $client] = mockClient([
        jsonResponse(404, ['error' => 'Not found']),
    ]);

    $client->domains->get('sd_nonexistent');
})->throws(InvalidRequestException::class);
