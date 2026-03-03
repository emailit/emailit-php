<?php

use Emailit\BaseEmailitClient;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;

pest()->afterEach(function () {
    Mockery::close();
});

/**
 * Create a mock Guzzle handler that returns the given responses in order.
 */
function mockHandler(Response ...$responses): MockHandler
{
    return new MockHandler($responses);
}

/**
 * Build an Emailit client with a mocked Guzzle transport.
 *
 * @param Response[] $responses  Queued PSR-7 responses the mock will return.
 * @return array{client: \Emailit\EmailitClient, handler: MockHandler}
 */
function mockClient(array $responses): array
{
    $handler = new MockHandler($responses);
    $stack = HandlerStack::create($handler);

    $guzzle = new GuzzleClient(['handler' => $stack, 'http_errors' => false]);

    $client = new \Emailit\EmailitClient([
        'api_key' => 'em_test_key',
        'http_client' => $guzzle,
    ]);

    return ['client' => $client, 'handler' => $handler];
}

/**
 * Shorthand: build a JSON PSR-7 response.
 */
function jsonResponse(int $status, array $body, array $headers = []): Response
{
    return new Response(
        $status,
        array_merge(['Content-Type' => 'application/json'], $headers),
        json_encode($body),
    );
}
