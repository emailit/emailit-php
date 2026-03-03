<?php

use Emailit\ApiResponse;
use Emailit\Collection;
use Emailit\EmailitObject;
use Emailit\EmailVerificationList;

// ──────────────────────────────────────────────────
// create()
// ──────────────────────────────────────────────────

test('create() returns an EmailVerificationList resource', function () {
    $body = [
        'object' => 'email_verification_list',
        'id' => 'evl_abc123',
        'name' => 'Marketing List Q1',
        'status' => 'pending',
        'stats' => [
            'total_emails' => 3,
            'processed_emails' => 0,
            'successful_verifications' => 0,
            'failed_verifications' => 0,
            'pending_emails' => 3,
        ],
        'created_at' => '2026-02-02T10:30:00.000Z',
    ];

    ['client' => $client, 'handler' => $handler] = mockClient([jsonResponse(201, $body)]);

    $list = $client->emailVerificationLists->create([
        'name' => 'Marketing List Q1',
        'emails' => ['user1@example.com', 'user2@example.com', 'user3@example.com'],
    ]);

    expect($list)->toBeInstanceOf(EmailVerificationList::class)
        ->and($list->id)->toBe('evl_abc123')
        ->and($list->name)->toBe('Marketing List Q1')
        ->and($list->status)->toBe('pending')
        ->and($list->stats['total_emails'])->toBe(3);

    $lastRequest = $handler->getLastRequest();
    expect($lastRequest->getMethod())->toBe('POST')
        ->and((string) $lastRequest->getUri())->toContain('/v2/email-verification-lists');

    $sentBody = json_decode((string) $lastRequest->getBody(), true);
    expect($sentBody['emails'])->toHaveCount(3);
});

// ──────────────────────────────────────────────────
// list()
// ──────────────────────────────────────────────────

test('list() returns a Collection of verification lists', function () {
    $body = [
        'data' => [
            [
                'object' => 'email_verification_list',
                'id' => 'evl_1',
                'name' => 'List A',
                'status' => 'completed',
                'stats' => ['total_emails' => 100],
            ],
            [
                'object' => 'email_verification_list',
                'id' => 'evl_2',
                'name' => 'List B',
                'status' => 'pending',
                'stats' => ['total_emails' => 50],
            ],
        ],
        'next_page_url' => '/v2/email-verification-lists?page=2&limit=10',
        'previous_page_url' => null,
    ];

    ['client' => $client] = mockClient([jsonResponse(200, $body)]);

    $collection = $client->emailVerificationLists->list(['page' => 1, 'limit' => 10]);

    expect($collection)->toBeInstanceOf(Collection::class)
        ->and($collection)->toHaveCount(2)
        ->and($collection->hasMore())->toBeTrue()
        ->and($collection->getData()[0])->toBeInstanceOf(EmailVerificationList::class)
        ->and($collection->getData()[0]->name)->toBe('List A');
});

// ──────────────────────────────────────────────────
// get()
// ──────────────────────────────────────────────────

test('get() returns an EmailVerificationList resource', function () {
    $body = [
        'object' => 'email_verification_list',
        'id' => 'evl_abc123',
        'name' => 'Marketing List Q1',
        'status' => 'completed',
        'stats' => [
            'total_emails' => 1000,
            'processed_emails' => 1000,
            'successful_verifications' => 950,
            'failed_verifications' => 50,
            'pending_emails' => 0,
        ],
    ];

    ['client' => $client, 'handler' => $handler] = mockClient([jsonResponse(200, $body)]);

    $list = $client->emailVerificationLists->get('evl_abc123');

    expect($list)->toBeInstanceOf(EmailVerificationList::class)
        ->and($list->id)->toBe('evl_abc123')
        ->and($list->status)->toBe('completed')
        ->and($list->stats['successful_verifications'])->toBe(950);

    $lastRequest = $handler->getLastRequest();
    expect((string) $lastRequest->getUri())->toContain('/v2/email-verification-lists/evl_abc123');
});

// ──────────────────────────────────────────────────
// results()
// ──────────────────────────────────────────────────

test('results() returns a Collection of verification results', function () {
    $body = [
        'data' => [
            [
                'id' => 'ev_xyz789',
                'email' => 'user1@example.com',
                'status' => 'completed',
                'result' => 'safe',
                'score' => 100,
                'risk' => 'low',
            ],
            [
                'id' => 'ev_def456',
                'email' => 'invalid@fake.xyz',
                'status' => 'completed',
                'result' => 'invalid',
                'score' => 0,
                'risk' => 'high',
            ],
        ],
        'next_page_url' => '/v2/email-verification-lists/evl_abc123/results?page=2&limit=50',
        'previous_page_url' => null,
    ];

    ['client' => $client, 'handler' => $handler] = mockClient([jsonResponse(200, $body)]);

    $results = $client->emailVerificationLists->results('evl_abc123', ['page' => 1, 'limit' => 50]);

    expect($results)->toBeInstanceOf(Collection::class)
        ->and($results)->toHaveCount(2)
        ->and($results->hasMore())->toBeTrue()
        ->and($results->getData()[0]->email)->toBe('user1@example.com')
        ->and($results->getData()[1]->result)->toBe('invalid');

    $lastRequest = $handler->getLastRequest();
    expect((string) $lastRequest->getUri())->toContain('/v2/email-verification-lists/evl_abc123/results');
});

// ──────────────────────────────────────────────────
// export()
// ──────────────────────────────────────────────────

test('export() returns a raw ApiResponse with binary content', function () {
    $xlsxContent = 'fake-xlsx-binary-content';

    ['client' => $client, 'handler' => $handler] = mockClient([
        new \GuzzleHttp\Psr7\Response(
            200,
            ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'],
            $xlsxContent,
        ),
    ]);

    $response = $client->emailVerificationLists->export('evl_abc123');

    expect($response)->toBeInstanceOf(ApiResponse::class)
        ->and($response->statusCode)->toBe(200)
        ->and($response->body)->toBe($xlsxContent)
        ->and($response->json)->toBeNull();

    $lastRequest = $handler->getLastRequest();
    expect($lastRequest->getMethod())->toBe('GET')
        ->and((string) $lastRequest->getUri())->toContain('/v2/email-verification-lists/evl_abc123/export');
});
