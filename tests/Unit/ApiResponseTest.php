<?php

use Emailit\ApiResponse;

test('parses json body', function () {
    $response = new ApiResponse(200, ['Content-Type' => ['application/json']], '{"id":"em_123"}');

    expect($response->statusCode)->toBe(200)
        ->and($response->body)->toBe('{"id":"em_123"}')
        ->and($response->json)->toBe(['id' => 'em_123'])
        ->and($response->headers)->toBe(['Content-Type' => ['application/json']]);
});

test('handles non-json body gracefully', function () {
    $response = new ApiResponse(200, [], 'plain text');

    expect($response->json)->toBeNull()
        ->and($response->body)->toBe('plain text');
});

test('handles empty body', function () {
    $response = new ApiResponse(204, [], '');

    expect($response->json)->toBeNull()
        ->and($response->body)->toBe('');
});

test('preserves status code', function () {
    $response = new ApiResponse(404, [], '{"error":"not found"}');

    expect($response->statusCode)->toBe(404)
        ->and($response->json)->toBe(['error' => 'not found']);
});
