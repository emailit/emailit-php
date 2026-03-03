<?php

use Emailit\EmailitObject;

// ──────────────────────────────────────────────────
// verify()
// ──────────────────────────────────────────────────

test('verify() returns an email verification result', function () {
    $body = [
        'id' => 'ev_123',
        'email' => 'test@example.com',
        'status' => 'valid',
        'score' => 0.95,
        'risk' => 'low',
        'result' => 'deliverable',
        'mode' => 'default',
        'checks' => [
            'domain' => true,
            'smtp' => true,
            'mx' => true,
        ],
        'address' => [
            'local' => 'test',
            'domain' => 'example.com',
        ],
        'did_you_mean' => null,
        'mx_records' => ['mx1.example.com'],
    ];

    ['client' => $client, 'handler' => $handler] = mockClient([jsonResponse(200, $body)]);

    $result = $client->emailVerifications->verify([
        'email' => 'test@example.com',
    ]);

    expect($result)->toBeInstanceOf(EmailitObject::class)
        ->and($result->email)->toBe('test@example.com')
        ->and($result->status)->toBe('valid')
        ->and($result->score)->toBe(0.95)
        ->and($result->risk)->toBe('low')
        ->and($result->checks)->toBeArray();

    $lastRequest = $handler->getLastRequest();
    expect($lastRequest->getMethod())->toBe('POST')
        ->and((string) $lastRequest->getUri())->toContain('/v2/email-verifications');
});

test('verify() with mode parameter', function () {
    $body = [
        'id' => 'ev_456',
        'email' => 'test@example.com',
        'status' => 'valid',
        'mode' => 'quick',
    ];

    ['client' => $client, 'handler' => $handler] = mockClient([jsonResponse(200, $body)]);

    $result = $client->emailVerifications->verify([
        'email' => 'test@example.com',
        'mode' => 'quick',
    ]);

    expect($result->mode)->toBe('quick');

    $sentBody = json_decode((string) $handler->getLastRequest()->getBody(), true);
    expect($sentBody['mode'])->toBe('quick');
});
