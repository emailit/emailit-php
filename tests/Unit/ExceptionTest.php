<?php

use Emailit\Exceptions\ApiErrorException;
use Emailit\Exceptions\AuthenticationException;
use Emailit\Exceptions\InvalidRequestException;
use Emailit\Exceptions\RateLimitException;
use Emailit\Exceptions\UnprocessableEntityException;
use Emailit\Exceptions\ApiConnectionException;

test('ApiErrorException stores all properties', function () {
    $e = new ApiErrorException(
        'something broke',
        500,
        '{"error":"server"}',
        ['error' => 'server'],
        ['X-Request-Id' => ['req_123']],
    );

    expect($e->getMessage())->toBe('something broke')
        ->and($e->getHttpStatus())->toBe(500)
        ->and($e->getHttpBody())->toBe('{"error":"server"}')
        ->and($e->getJsonBody())->toBe(['error' => 'server'])
        ->and($e->getHttpHeaders())->toBe(['X-Request-Id' => ['req_123']])
        ->and($e->getCode())->toBe(500);
});

test('ApiErrorException preserves previous exception', function () {
    $previous = new \RuntimeException('root cause');
    $e = new ApiErrorException('wrapped', previous: $previous);

    expect($e->getPrevious())->toBe($previous);
});

test('factory returns AuthenticationException for 401', function () {
    $e = ApiErrorException::factory('unauthorized', 401, '', null, []);

    expect($e)->toBeInstanceOf(AuthenticationException::class)
        ->and($e->getHttpStatus())->toBe(401);
});

test('factory returns InvalidRequestException for 400', function () {
    $e = ApiErrorException::factory('bad request', 400, '', null, []);

    expect($e)->toBeInstanceOf(InvalidRequestException::class)
        ->and($e->getHttpStatus())->toBe(400);
});

test('factory returns InvalidRequestException for 404', function () {
    $e = ApiErrorException::factory('not found', 404, '', null, []);

    expect($e)->toBeInstanceOf(InvalidRequestException::class)
        ->and($e->getHttpStatus())->toBe(404);
});

test('factory returns RateLimitException for 429', function () {
    $e = ApiErrorException::factory('rate limited', 429, '', null, []);

    expect($e)->toBeInstanceOf(RateLimitException::class)
        ->and($e->getHttpStatus())->toBe(429);
});

test('factory returns UnprocessableEntityException for 422', function () {
    $e = ApiErrorException::factory('unprocessable', 422, '', null, []);

    expect($e)->toBeInstanceOf(UnprocessableEntityException::class)
        ->and($e->getHttpStatus())->toBe(422);
});

test('factory returns base ApiErrorException for unknown status', function () {
    $e = ApiErrorException::factory('server error', 500, '', null, []);

    expect($e)->toBeInstanceOf(ApiErrorException::class)
        ->and(get_class($e))->toBe(ApiErrorException::class)
        ->and($e->getHttpStatus())->toBe(500);
});

test('factory returns base ApiErrorException for 503', function () {
    $e = ApiErrorException::factory('unavailable', 503, '', null, []);

    expect($e)->toBeInstanceOf(ApiErrorException::class)
        ->and(get_class($e))->toBe(ApiErrorException::class);
});

test('factory passes json body through', function () {
    $json = ['error' => 'Rate limit exceeded', 'retry_after' => 1];
    $body = json_encode($json);

    $e = ApiErrorException::factory('rate limited', 429, $body, $json, ['Retry-After' => ['1']]);

    expect($e->getJsonBody())->toBe($json)
        ->and($e->getHttpBody())->toBe($body)
        ->and($e->getHttpHeaders())->toBe(['Retry-After' => ['1']]);
});

test('all exception subclasses extend ApiErrorException', function () {
    $subclasses = [
        AuthenticationException::class,
        InvalidRequestException::class,
        RateLimitException::class,
        UnprocessableEntityException::class,
        ApiConnectionException::class,
    ];

    foreach ($subclasses as $class) {
        expect(is_subclass_of($class, ApiErrorException::class))->toBeTrue(
            "{$class} should extend " . ApiErrorException::class,
        );
    }
});
