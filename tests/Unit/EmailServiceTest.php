<?php

use Emailit\Email;
use Emailit\EmailitObject;
use Emailit\Collection;
use Emailit\Exceptions\AuthenticationException;
use Emailit\Exceptions\InvalidRequestException;
use Emailit\Exceptions\RateLimitException;
use Emailit\Exceptions\UnprocessableEntityException;
use Emailit\Exceptions\ApiConnectionException;

// ──────────────────────────────────────────────────
// send()
// ──────────────────────────────────────────────────

test('send() returns an Email resource', function () {
    $responseBody = [
        'object' => 'email',
        'id' => 'em_abc123',
        'status' => 'pending',
        'from' => 'hello@example.com',
        'to' => ['user@example.com'],
        'subject' => 'Hello World',
    ];

    ['client' => $client, 'handler' => $handler] = mockClient([
        jsonResponse(201, $responseBody),
    ]);

    $email = $client->emails()->send([
        'from' => 'hello@example.com',
        'to' => ['user@example.com'],
        'subject' => 'Hello World',
        'html' => '<h1>Welcome!</h1>',
    ]);

    expect($email)->toBeInstanceOf(Email::class)
        ->and($email->id)->toBe('em_abc123')
        ->and($email->status)->toBe('pending')
        ->and($email->from)->toBe('hello@example.com')
        ->and($email->to)->toBe(['user@example.com'])
        ->and($email->subject)->toBe('Hello World')
        ->and($email['id'])->toBe('em_abc123');

    $lastRequest = $handler->getLastRequest();
    expect($lastRequest->getMethod())->toBe('POST')
        ->and((string) $lastRequest->getUri())->toBe('/v2/emails');

    $sentBody = json_decode((string) $lastRequest->getBody(), true);
    expect($sentBody['from'])->toBe('hello@example.com')
        ->and($sentBody['html'])->toBe('<h1>Welcome!</h1>');
});

test('send() with template and variables', function () {
    ['client' => $client, 'handler' => $handler] = mockClient([
        jsonResponse(201, ['object' => 'email', 'id' => 'em_tpl1', 'status' => 'pending']),
    ]);

    $email = $client->emails()->send([
        'from' => 'hello@example.com',
        'to' => 'user@example.com',
        'template' => 'welcome_email',
        'variables' => ['name' => 'John', 'company' => 'Acme'],
    ]);

    expect($email)->toBeInstanceOf(Email::class)
        ->and($email->id)->toBe('em_tpl1');

    $sentBody = json_decode((string) $handler->getLastRequest()->getBody(), true);
    expect($sentBody['template'])->toBe('welcome_email')
        ->and($sentBody['variables'])->toBe(['name' => 'John', 'company' => 'Acme']);
});

test('send() with attachments', function () {
    ['client' => $client, 'handler' => $handler] = mockClient([
        jsonResponse(201, ['object' => 'email', 'id' => 'em_att1', 'status' => 'pending']),
    ]);

    $client->emails()->send([
        'from' => 'invoices@example.com',
        'to' => 'customer@example.com',
        'subject' => 'Invoice',
        'html' => '<p>See attached.</p>',
        'attachments' => [
            ['filename' => 'invoice.pdf', 'content' => 'base64data', 'content_type' => 'application/pdf'],
        ],
    ]);

    $sentBody = json_decode((string) $handler->getLastRequest()->getBody(), true);
    expect($sentBody['attachments'])->toHaveCount(1)
        ->and($sentBody['attachments'][0]['filename'])->toBe('invoice.pdf');
});

test('send() with scheduled_at returns scheduled status', function () {
    ['client' => $client] = mockClient([
        jsonResponse(201, ['object' => 'email', 'id' => 'em_sch1', 'status' => 'scheduled']),
    ]);

    $email = $client->emails()->send([
        'from' => 'reminders@example.com',
        'to' => 'user@example.com',
        'subject' => 'Reminder',
        'html' => '<p>Tomorrow at 2 PM.</p>',
        'scheduled_at' => '2026-01-10T09:00:00Z',
    ]);

    expect($email->status)->toBe('scheduled');
});

test('send() with tracking options', function () {
    ['client' => $client, 'handler' => $handler] = mockClient([
        jsonResponse(201, ['object' => 'email', 'id' => 'em_trk1', 'status' => 'pending']),
    ]);

    $client->emails()->send([
        'from' => 'hello@example.com',
        'to' => ['user@example.com'],
        'subject' => 'Tracked',
        'html' => '<p>Hi</p>',
        'tracking' => ['loads' => true, 'clicks' => true],
    ]);

    $sentBody = json_decode((string) $handler->getLastRequest()->getBody(), true);
    expect($sentBody['tracking'])->toBe(['loads' => true, 'clicks' => true]);
});

// ──────────────────────────────────────────────────
// list()
// ──────────────────────────────────────────────────

test('list() returns a Collection of Email resources', function () {
    $responseBody = [
        'data' => [
            ['object' => 'email', 'id' => 'em_1', 'status' => 'delivered'],
            ['object' => 'email', 'id' => 'em_2', 'status' => 'pending'],
        ],
        'next_page_url' => '/v2/emails?page=2&limit=10',
        'previous_page_url' => null,
    ];

    ['client' => $client, 'handler' => $handler] = mockClient([
        jsonResponse(200, $responseBody),
    ]);

    $list = $client->emails()->list(['page' => 1, 'limit' => 10]);

    expect($list)->toBeInstanceOf(Collection::class)
        ->and($list)->toHaveCount(2)
        ->and($list->hasMore())->toBeTrue()
        ->and($list->getData()[0])->toBeInstanceOf(Email::class)
        ->and($list->getData()[0]->id)->toBe('em_1')
        ->and($list->getData()[1]->id)->toBe('em_2');

    $lastRequest = $handler->getLastRequest();
    expect($lastRequest->getMethod())->toBe('GET')
        ->and($lastRequest->getUri()->getQuery())->toBe('page=1&limit=10');
});

test('list() without params sends no query string', function () {
    ['client' => $client, 'handler' => $handler] = mockClient([
        jsonResponse(200, ['data' => [], 'next_page_url' => null, 'previous_page_url' => null]),
    ]);

    $list = $client->emails()->list();

    expect($list)->toBeInstanceOf(Collection::class)
        ->and($list)->toHaveCount(0)
        ->and($list->hasMore())->toBeFalse();
    expect($handler->getLastRequest()->getUri()->getQuery())->toBe('');
});

// ──────────────────────────────────────────────────
// get()
// ──────────────────────────────────────────────────

test('get() returns a typed Email resource', function () {
    $responseBody = [
        'object' => 'email',
        'id' => 'em_abc123',
        'type' => 'outbound',
        'status' => 'delivered',
        'from' => 'sender@example.com',
        'to' => 'recipient@example.com',
        'subject' => 'Hello World',
    ];

    ['client' => $client, 'handler' => $handler] = mockClient([
        jsonResponse(200, $responseBody),
    ]);

    $email = $client->emails()->get('em_abc123');

    expect($email)->toBeInstanceOf(Email::class)
        ->and($email->id)->toBe('em_abc123')
        ->and($email->type)->toBe('outbound')
        ->and($email->status)->toBe('delivered');

    expect($handler->getLastRequest()->getMethod())->toBe('GET')
        ->and((string) $handler->getLastRequest()->getUri())->toBe('/v2/emails/em_abc123');
});

// ──────────────────────────────────────────────────
// getRaw()
// ──────────────────────────────────────────────────

test('getRaw() returns Email with raw property', function () {
    $responseBody = [
        'object' => 'email',
        'id' => 'em_abc123',
        'raw' => "From: sender@example.com\r\nTo: recipient@example.com\r\nSubject: Hello\r\n\r\nBody",
    ];

    ['client' => $client, 'handler' => $handler] = mockClient([
        jsonResponse(200, $responseBody),
    ]);

    $email = $client->emails()->getRaw('em_abc123');

    expect($email)->toBeInstanceOf(Email::class)
        ->and($email->raw)->toContain('From: sender@example.com');
    expect((string) $handler->getLastRequest()->getUri())->toBe('/v2/emails/em_abc123/raw');
});

// ──────────────────────────────────────────────────
// getAttachments()
// ──────────────────────────────────────────────────

test('getAttachments() returns a Collection', function () {
    $responseBody = [
        'object' => 'list',
        'data' => [
            ['filename' => 'doc.pdf', 'content_type' => 'application/pdf', 'size' => 12345],
        ],
    ];

    ['client' => $client, 'handler' => $handler] = mockClient([
        jsonResponse(200, $responseBody),
    ]);

    $attachments = $client->emails()->getAttachments('em_abc123');

    expect($attachments)->toBeInstanceOf(Collection::class)
        ->and($attachments)->toHaveCount(1)
        ->and($attachments->getData()[0]->filename)->toBe('doc.pdf');
    expect((string) $handler->getLastRequest()->getUri())->toBe('/v2/emails/em_abc123/attachments');
});

// ──────────────────────────────────────────────────
// getBody()
// ──────────────────────────────────────────────────

test('getBody() returns EmailitObject with text and html', function () {
    $responseBody = [
        'text' => 'Plain text content',
        'html' => '<html><body><h1>Welcome!</h1></body></html>',
    ];

    ['client' => $client, 'handler' => $handler] = mockClient([
        jsonResponse(200, $responseBody),
    ]);

    $body = $client->emails()->getBody('em_abc123');

    expect($body)->toBeInstanceOf(EmailitObject::class)
        ->and($body->text)->toBe('Plain text content')
        ->and($body->html)->toContain('<h1>Welcome!</h1>');
    expect((string) $handler->getLastRequest()->getUri())->toBe('/v2/emails/em_abc123/body');
});

// ──────────────────────────────────────────────────
// getMeta()
// ──────────────────────────────────────────────────

test('getMeta() returns an Email resource with headers', function () {
    $responseBody = [
        'object' => 'email',
        'id' => 'em_abc123',
        'headers' => ['From' => 'sender@example.com', 'Subject' => 'Hello'],
        'attachments' => [['filename' => 'doc.pdf', 'size' => 12345]],
    ];

    ['client' => $client, 'handler' => $handler] = mockClient([
        jsonResponse(200, $responseBody),
    ]);

    $meta = $client->emails()->getMeta('em_abc123');

    expect($meta)->toBeInstanceOf(Email::class)
        ->and($meta->headers['From'])->toBe('sender@example.com')
        ->and($meta->attachments)->toHaveCount(1);
    expect((string) $handler->getLastRequest()->getUri())->toBe('/v2/emails/em_abc123/meta');
});

// ──────────────────────────────────────────────────
// update()
// ──────────────────────────────────────────────────

test('update() returns updated Email resource', function () {
    $responseBody = [
        'object' => 'email',
        'id' => 'em_abc123',
        'status' => 'scheduled',
        'scheduled_at' => '2026-01-10T15:00:00.000Z',
        'message' => 'Email schedule has been updated successfully',
    ];

    ['client' => $client, 'handler' => $handler] = mockClient([
        jsonResponse(200, $responseBody),
    ]);

    $email = $client->emails()->update('em_abc123', ['scheduled_at' => '2026-01-10T15:00:00Z']);

    expect($email)->toBeInstanceOf(Email::class)
        ->and($email->status)->toBe('scheduled')
        ->and($email->message)->toContain('updated successfully');

    $lastRequest = $handler->getLastRequest();
    expect($lastRequest->getMethod())->toBe('POST')
        ->and((string) $lastRequest->getUri())->toBe('/v2/emails/em_abc123');
});

// ──────────────────────────────────────────────────
// cancel()
// ──────────────────────────────────────────────────

test('cancel() returns canceled Email resource', function () {
    $responseBody = [
        'object' => 'email',
        'id' => 'em_abc123',
        'status' => 'canceled',
        'message' => 'Email has been canceled successfully',
    ];

    ['client' => $client, 'handler' => $handler] = mockClient([
        jsonResponse(200, $responseBody),
    ]);

    $email = $client->emails()->cancel('em_abc123');

    expect($email)->toBeInstanceOf(Email::class)
        ->and($email->status)->toBe('canceled');

    expect($handler->getLastRequest()->getMethod())->toBe('POST')
        ->and((string) $handler->getLastRequest()->getUri())->toBe('/v2/emails/em_abc123/cancel');
});

// ──────────────────────────────────────────────────
// retry()
// ──────────────────────────────────────────────────

test('retry() returns new Email resource with original_id', function () {
    $responseBody = [
        'object' => 'email',
        'id' => 'em_new789',
        'original_id' => 'em_abc123',
        'status' => 'pending',
        'message' => 'Email has been queued for retry',
    ];

    ['client' => $client, 'handler' => $handler] = mockClient([
        jsonResponse(200, $responseBody),
    ]);

    $email = $client->emails()->retry('em_abc123');

    expect($email)->toBeInstanceOf(Email::class)
        ->and($email->original_id)->toBe('em_abc123')
        ->and($email->id)->toBe('em_new789')
        ->and($email->status)->toBe('pending');

    expect($handler->getLastRequest()->getMethod())->toBe('POST')
        ->and((string) $handler->getLastRequest()->getUri())->toBe('/v2/emails/em_abc123/retry');
});

// ──────────────────────────────────────────────────
// Resource features
// ──────────────────────────────────────────────────

test('Email resource supports ArrayAccess', function () {
    ['client' => $client] = mockClient([
        jsonResponse(200, ['object' => 'email', 'id' => 'em_abc', 'status' => 'sent']),
    ]);

    $email = $client->emails()->get('em_abc');

    expect($email['id'])->toBe('em_abc')
        ->and($email['status'])->toBe('sent')
        ->and(isset($email['id']))->toBeTrue()
        ->and(isset($email['nonexistent']))->toBeFalse();
});

test('Email resource supports toArray()', function () {
    ['client' => $client] = mockClient([
        jsonResponse(200, ['object' => 'email', 'id' => 'em_abc', 'status' => 'sent']),
    ]);

    $email = $client->emails()->get('em_abc');

    expect($email->toArray())->toBe(['object' => 'email', 'id' => 'em_abc', 'status' => 'sent']);
});

test('Email resource is JSON serializable', function () {
    ['client' => $client] = mockClient([
        jsonResponse(200, ['object' => 'email', 'id' => 'em_abc', 'status' => 'sent']),
    ]);

    $email = $client->emails()->get('em_abc');

    expect(json_encode($email))->toBe('{"object":"email","id":"em_abc","status":"sent"}');
});

test('getLastResponse() returns the underlying ApiResponse', function () {
    ['client' => $client] = mockClient([
        jsonResponse(200, ['object' => 'email', 'id' => 'em_abc']),
    ]);

    $email = $client->emails()->get('em_abc');

    expect($email->getLastResponse())->toBeInstanceOf(\Emailit\ApiResponse::class)
        ->and($email->getLastResponse()->statusCode)->toBe(200);
});

test('Collection is iterable', function () {
    ['client' => $client] = mockClient([
        jsonResponse(200, [
            'data' => [
                ['object' => 'email', 'id' => 'em_1'],
                ['object' => 'email', 'id' => 'em_2'],
            ],
            'next_page_url' => null,
            'previous_page_url' => null,
        ]),
    ]);

    $list = $client->emails()->list();
    $ids = [];

    foreach ($list as $email) {
        $ids[] = $email->id;
    }

    expect($ids)->toBe(['em_1', 'em_2']);
});

// ──────────────────────────────────────────────────
// URL encoding
// ──────────────────────────────────────────────────

test('email id is url-encoded in path', function () {
    ['client' => $client, 'handler' => $handler] = mockClient([
        jsonResponse(200, ['object' => 'email', 'id' => 'em_with spaces']),
    ]);

    $client->emails()->get('em_with spaces');

    expect((string) $handler->getLastRequest()->getUri())->toBe('/v2/emails/em_with+spaces');
});

// ──────────────────────────────────────────────────
// Error handling
// ──────────────────────────────────────────────────

test('401 throws AuthenticationException', function () {
    ['client' => $client] = mockClient([
        jsonResponse(401, ['error' => 'Unauthorized', 'message' => 'Invalid API key']),
    ]);

    $client->emails()->list();
})->throws(AuthenticationException::class, 'Unauthorized: Invalid API key');

test('400 throws InvalidRequestException', function () {
    ['client' => $client] = mockClient([
        jsonResponse(400, ['error' => 'Validation failed', 'message' => 'Missing required field: from']),
    ]);

    $client->emails()->send(['to' => ['user@example.com']]);
})->throws(InvalidRequestException::class, 'Validation failed: Missing required field: from');

test('404 throws InvalidRequestException', function () {
    ['client' => $client] = mockClient([
        jsonResponse(404, ['error' => 'Email not found', 'message' => "Email with ID 'em_fake' not found"]),
    ]);

    $client->emails()->get('em_fake');
})->throws(InvalidRequestException::class, 'Email not found');

test('422 throws UnprocessableEntityException', function () {
    ['client' => $client] = mockClient([
        jsonResponse(422, ['error' => 'Cannot cancel email', 'message' => "Current status: 'sent'"]),
    ]);

    $client->emails()->cancel('em_sent');
})->throws(UnprocessableEntityException::class, 'Cannot cancel email');

test('429 throws RateLimitException', function () {
    ['client' => $client] = mockClient([
        jsonResponse(429, ['error' => 'Rate limit exceeded', 'message' => 'Too many requests']),
    ]);

    $client->emails()->send(['from' => 'a@b.com', 'to' => 'c@d.com', 'subject' => 'Hi', 'html' => 'Hi']);
})->throws(RateLimitException::class, 'Rate limit exceeded: Too many requests');

test('error exception carries full response data', function () {
    $errorBody = ['error' => 'Email not found', 'message' => 'Not found in workspace'];

    ['client' => $client] = mockClient([
        jsonResponse(404, $errorBody, ['X-Request-Id' => 'req_test123']),
    ]);

    try {
        $client->emails()->get('em_nonexistent');
        $this->fail('Expected exception');
    } catch (InvalidRequestException $e) {
        expect($e->getHttpStatus())->toBe(404)
            ->and($e->getJsonBody())->toBe($errorBody)
            ->and($e->getHttpBody())->toBe(json_encode($errorBody))
            ->and($e->getHttpHeaders())->toHaveKey('X-Request-Id');
    }
});

test('nested error format is extracted', function () {
    ['client' => $client] = mockClient([
        jsonResponse(400, ['error' => ['type' => 'validation_error', 'message' => 'The recipient email address is invalid', 'param' => 'to']]),
    ]);

    try {
        $client->emails()->send(['from' => 'a@b.com', 'to' => 'invalid', 'subject' => 'Hi', 'html' => 'Hi']);
        $this->fail('Expected exception');
    } catch (InvalidRequestException $e) {
        expect($e->getMessage())->toBe('The recipient email address is invalid');
    }
});

test('non-json error body uses fallback message', function () {
    ['client' => $client] = mockClient([
        new \GuzzleHttp\Psr7\Response(502, [], 'Bad Gateway'),
    ]);

    try {
        $client->emails()->list();
        $this->fail('Expected exception');
    } catch (\Emailit\Exceptions\ApiErrorException $e) {
        expect($e->getMessage())->toBe('API request failed with status 502')
            ->and($e->getHttpBody())->toBe('Bad Gateway');
    }
});

test('connection failure throws ApiConnectionException', function () {
    $handler = new \GuzzleHttp\Handler\MockHandler([
        new \GuzzleHttp\Exception\ConnectException(
            'Connection refused',
            new \GuzzleHttp\Psr7\Request('GET', '/v2/emails'),
        ),
    ]);

    $stack = \GuzzleHttp\HandlerStack::create($handler);
    $guzzle = new \GuzzleHttp\Client(['handler' => $stack]);

    $client = new \Emailit\EmailitClient([
        'api_key' => 'em_test_key',
        'http_client' => $guzzle,
    ]);

    $client->emails()->list();
})->throws(ApiConnectionException::class, 'Could not connect to the Emailit API');
