<?php

use Emailit\EmailitClient;
use Emailit\BaseEmailitClient;
use Emailit\Service\EmailService;

test('accepts string api key', function () {
    $client = new EmailitClient('em_test_abc');

    expect($client->getApiKey())->toBe('em_test_abc')
        ->and($client->getApiBase())->toBe(BaseEmailitClient::DEFAULT_API_BASE);
});

test('accepts config array', function () {
    $client = new EmailitClient([
        'api_key' => 'em_test_xyz',
        'api_base' => 'https://custom.emailit.com',
    ]);

    expect($client->getApiKey())->toBe('em_test_xyz')
        ->and($client->getApiBase())->toBe('https://custom.emailit.com');
});

test('strips trailing slash from api_base', function () {
    $client = new EmailitClient([
        'api_key' => 'em_test_key',
        'api_base' => 'https://api.emailit.com/',
    ]);

    expect($client->getApiBase())->toBe('https://api.emailit.com');
});

test('throws on missing api_key', function () {
    new EmailitClient(['api_base' => 'https://api.emailit.com']);
})->throws(\InvalidArgumentException::class, 'api_key is required');

test('throws on empty api_key string', function () {
    new EmailitClient('');
})->throws(\InvalidArgumentException::class, 'api_key is required');

test('throws on empty api_key in array', function () {
    new EmailitClient(['api_key' => '']);
})->throws(\InvalidArgumentException::class, 'api_key is required');

test('emails property returns EmailService', function () {
    $client = new EmailitClient('em_test_key');

    expect($client->emails)->toBeInstanceOf(EmailService::class);
});

test('emails service is cached', function () {
    $client = new EmailitClient('em_test_key');

    $first = $client->emails;
    $second = $client->emails;

    expect($first)->toBe($second);
});

test('Emailit::client() returns EmailitClient', function () {
    $client = Emailit::client('em_test_facade');

    expect($client)->toBeInstanceOf(EmailitClient::class)
        ->and($client->getApiKey())->toBe('em_test_facade');
});

test('Emailit::VERSION is defined', function () {
    expect(Emailit::VERSION)->toBe('1.0.0');
});

test('unknown property triggers notice and returns null', function () {
    $client = new EmailitClient('em_test_key');

    set_error_handler(fn () => true);
    $result = $client->nonexistent;
    restore_error_handler();

    expect($result)->toBeNull();
});
