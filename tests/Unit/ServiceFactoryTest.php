<?php

use Emailit\EmailitClient;
use Emailit\Service\CoreServiceFactory;
use Emailit\Service\EmailService;
use Emailit\Service\DomainService;
use Emailit\Service\ApiKeyService;
use Emailit\Service\AudienceService;
use Emailit\Service\SubscriberService;
use Emailit\Service\TemplateService;
use Emailit\Service\SuppressionService;
use Emailit\Service\EmailVerificationListService;
use Emailit\Service\EmailVerificationService;
use Emailit\Service\WebhookService;
use Emailit\Service\ContactService;
use Emailit\Service\EventService;
use Emailit\Service\AbstractServiceFactory;

test('CoreServiceFactory returns EmailService for emails', function () {
    $client = new EmailitClient('em_test_key');
    $factory = new CoreServiceFactory($client);

    $service = $factory->getService('emails');

    expect($service)->toBeInstanceOf(EmailService::class);
});

test('CoreServiceFactory resolves all registered services', function () {
    $client = new EmailitClient('em_test_key');
    $factory = new CoreServiceFactory($client);

    expect($factory->getService('emails'))->toBeInstanceOf(EmailService::class)
        ->and($factory->getService('domains'))->toBeInstanceOf(DomainService::class)
        ->and($factory->getService('apiKeys'))->toBeInstanceOf(ApiKeyService::class)
        ->and($factory->getService('audiences'))->toBeInstanceOf(AudienceService::class)
        ->and($factory->getService('subscribers'))->toBeInstanceOf(SubscriberService::class)
        ->and($factory->getService('templates'))->toBeInstanceOf(TemplateService::class)
        ->and($factory->getService('suppressions'))->toBeInstanceOf(SuppressionService::class)
        ->and($factory->getService('emailVerifications'))->toBeInstanceOf(EmailVerificationService::class)
        ->and($factory->getService('emailVerificationLists'))->toBeInstanceOf(EmailVerificationListService::class)
        ->and($factory->getService('webhooks'))->toBeInstanceOf(WebhookService::class)
        ->and($factory->getService('contacts'))->toBeInstanceOf(ContactService::class)
        ->and($factory->getService('events'))->toBeInstanceOf(EventService::class);
});

test('CoreServiceFactory caches service instances', function () {
    $client = new EmailitClient('em_test_key');
    $factory = new CoreServiceFactory($client);

    $first = $factory->getService('emails');
    $second = $factory->getService('emails');

    expect($first)->toBe($second);
});

test('CoreServiceFactory returns null for unknown service', function () {
    $client = new EmailitClient('em_test_key');
    $factory = new CoreServiceFactory($client);

    expect($factory->getService('nonexistent'))->toBeNull();
});

test('CoreServiceFactory supports __get magic accessor', function () {
    $client = new EmailitClient('em_test_key');
    $factory = new CoreServiceFactory($client);

    expect($factory->emails)->toBeInstanceOf(EmailService::class)
        ->and($factory->unknown)->toBeNull();
});
