<?php

use Emailit\WebhookSignature;
use Emailit\Events\WebhookEvent;
use Emailit\Events\EmailDelivered;
use Emailit\Exceptions\ApiErrorException;

// ──────────────────────────────────────────────────
// verify() - valid signature
// ──────────────────────────────────────────────────

test('verify returns typed event with valid signature', function () {
    $secret = 'whsec_test_secret';
    $payload = json_encode([
        'event_id' => 'evt_abc123',
        'type' => 'email.delivered',
        'data' => ['object' => ['id' => 'em_xyz']],
    ]);
    $timestamp = (string) time();
    $signature = WebhookSignature::computeSignature($payload, $timestamp, $secret);

    $event = WebhookSignature::verify($payload, $signature, $timestamp, $secret);

    expect($event)->toBeInstanceOf(EmailDelivered::class)
        ->and($event->event_id)->toBe('evt_abc123')
        ->and($event->type)->toBe('email.delivered');
});

// ──────────────────────────────────────────────────
// verify() - invalid signature
// ──────────────────────────────────────────────────

test('verify throws on invalid signature', function () {
    $secret = 'whsec_test_secret';
    $payload = json_encode(['event_id' => 'evt_abc123', 'type' => 'email.delivered']);
    $timestamp = (string) time();

    WebhookSignature::verify($payload, 'invalid_signature_hex', $timestamp, $secret);
})->throws(ApiErrorException::class, 'Webhook signature verification failed.');

// ──────────────────────────────────────────────────
// verify() - replay attack (timestamp too old)
// ──────────────────────────────────────────────────

test('verify throws when timestamp is too old', function () {
    $secret = 'whsec_test_secret';
    $payload = json_encode(['event_id' => 'evt_abc123', 'type' => 'email.delivered']);
    $timestamp = (string) (time() - 600); // 10 minutes ago
    $signature = WebhookSignature::computeSignature($payload, $timestamp, $secret);

    WebhookSignature::verify($payload, $signature, $timestamp, $secret);
})->throws(ApiErrorException::class, 'Webhook timestamp is too old');

// ──────────────────────────────────────────────────
// verify() - tolerance = null skips replay check
// ──────────────────────────────────────────────────

test('verify with null tolerance skips age check', function () {
    $secret = 'whsec_test_secret';
    $payload = json_encode([
        'event_id' => 'evt_old',
        'type' => 'email.bounced',
        'data' => ['object' => ['id' => 'em_1']],
    ]);
    $timestamp = (string) (time() - 86400); // 24 hours ago
    $signature = WebhookSignature::computeSignature($payload, $timestamp, $secret);

    $event = WebhookSignature::verify($payload, $signature, $timestamp, $secret, tolerance: null);

    expect($event)->toBeInstanceOf(WebhookEvent::class)
        ->and($event->event_id)->toBe('evt_old');
});

// ──────────────────────────────────────────────────
// verify() - custom tolerance
// ──────────────────────────────────────────────────

test('verify respects custom tolerance', function () {
    $secret = 'whsec_test_secret';
    $payload = json_encode(['event_id' => 'evt_test', 'type' => 'email.delivered']);
    $timestamp = (string) (time() - 120); // 2 minutes ago
    $signature = WebhookSignature::computeSignature($payload, $timestamp, $secret);

    // 60 second tolerance should reject 2 min old request
    WebhookSignature::verify($payload, $signature, $timestamp, $secret, tolerance: 60);
})->throws(ApiErrorException::class, 'Webhook timestamp is too old');

test('verify passes with sufficient custom tolerance', function () {
    $secret = 'whsec_test_secret';
    $payload = json_encode([
        'event_id' => 'evt_test',
        'type' => 'email.delivered',
        'data' => ['object' => ['id' => 'em_1']],
    ]);
    $timestamp = (string) (time() - 120); // 2 minutes ago
    $signature = WebhookSignature::computeSignature($payload, $timestamp, $secret);

    // 300 second tolerance should accept 2 min old request
    $event = WebhookSignature::verify($payload, $signature, $timestamp, $secret, tolerance: 300);

    expect($event)->toBeInstanceOf(EmailDelivered::class);
});

// ──────────────────────────────────────────────────
// verify() - invalid JSON
// ──────────────────────────────────────────────────

test('verify throws on invalid JSON body', function () {
    $secret = 'whsec_test_secret';
    $payload = 'not-valid-json{{{';
    $timestamp = (string) time();
    $signature = WebhookSignature::computeSignature($payload, $timestamp, $secret);

    WebhookSignature::verify($payload, $signature, $timestamp, $secret);
})->throws(ApiErrorException::class, 'Invalid webhook payload');

// ──────────────────────────────────────────────────
// computeSignature()
// ──────────────────────────────────────────────────

test('computeSignature produces deterministic HMAC', function () {
    $body = '{"event_id":"evt_123"}';
    $timestamp = '1700000000';
    $secret = 'test_secret';

    $sig1 = WebhookSignature::computeSignature($body, $timestamp, $secret);
    $sig2 = WebhookSignature::computeSignature($body, $timestamp, $secret);

    expect($sig1)->toBe($sig2)
        ->and(strlen($sig1))->toBe(64); // SHA-256 hex = 64 chars
});

test('computeSignature changes with different secrets', function () {
    $body = '{"event_id":"evt_123"}';
    $timestamp = '1700000000';

    $sig1 = WebhookSignature::computeSignature($body, $timestamp, 'secret_a');
    $sig2 = WebhookSignature::computeSignature($body, $timestamp, 'secret_b');

    expect($sig1)->not->toBe($sig2);
});

test('computeSignature changes with different timestamps', function () {
    $body = '{"event_id":"evt_123"}';
    $secret = 'test_secret';

    $sig1 = WebhookSignature::computeSignature($body, '1700000000', $secret);
    $sig2 = WebhookSignature::computeSignature($body, '1700000001', $secret);

    expect($sig1)->not->toBe($sig2);
});

// ──────────────────────────────────────────────────
// Constants
// ──────────────────────────────────────────────────

test('header constants are defined', function () {
    expect(WebhookSignature::HEADER_SIGNATURE)->toBe('x-emailit-signature')
        ->and(WebhookSignature::HEADER_TIMESTAMP)->toBe('x-emailit-timestamp')
        ->and(WebhookSignature::DEFAULT_TOLERANCE)->toBe(300);
});
