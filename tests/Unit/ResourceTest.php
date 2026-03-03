<?php

use Emailit\ApiKey;
use Emailit\Audience;
use Emailit\Contact;
use Emailit\Domain;
use Emailit\Email;
use Emailit\EmailVerification;
use Emailit\EmailVerificationList;
use Emailit\Event;
use Emailit\Subscriber;
use Emailit\Suppression;
use Emailit\Template;
use Emailit\Webhook;
use Emailit\ApiResource;
use Emailit\EmailitObject;
use Emailit\ApiResponse;
use Emailit\Collection;
use Emailit\Util\Util;

// ──────────────────────────────────────────────────
// EmailitObject
// ──────────────────────────────────────────────────

test('EmailitObject supports dynamic property access', function () {
    $obj = new EmailitObject(['id' => 'test_123', 'name' => 'hello']);

    expect($obj->id)->toBe('test_123')
        ->and($obj->name)->toBe('hello')
        ->and($obj->missing)->toBeNull();
});

test('EmailitObject supports ArrayAccess', function () {
    $obj = new EmailitObject(['key' => 'value']);

    expect($obj['key'])->toBe('value')
        ->and(isset($obj['key']))->toBeTrue()
        ->and(isset($obj['missing']))->toBeFalse();

    $obj['new'] = 'data';
    expect($obj['new'])->toBe('data');

    unset($obj['new']);
    expect(isset($obj['new']))->toBeFalse();
});

test('EmailitObject toArray returns all values', function () {
    $data = ['a' => 1, 'b' => 2];
    $obj = new EmailitObject($data);

    expect($obj->toArray())->toBe($data);
});

test('EmailitObject is JSON serializable', function () {
    $obj = new EmailitObject(['id' => 'x']);

    expect(json_encode($obj))->toBe('{"id":"x"}');
});

test('EmailitObject __toString returns pretty JSON', function () {
    $obj = new EmailitObject(['id' => 'x']);

    expect((string) $obj)->toContain('"id": "x"');
});

test('EmailitObject refreshFrom replaces values', function () {
    $obj = new EmailitObject(['old' => 'data']);
    $obj->refreshFrom(['new' => 'data']);

    expect($obj->old)->toBeNull()
        ->and($obj->new)->toBe('data');
});

test('EmailitObject stores last response', function () {
    $obj = new EmailitObject();
    $response = new ApiResponse(200, [], '{}');

    expect($obj->getLastResponse())->toBeNull();

    $obj->setLastResponse($response);
    expect($obj->getLastResponse())->toBe($response);
});

// ──────────────────────────────────────────────────
// ApiResource
// ──────────────────────────────────────────────────

test('Email::OBJECT_NAME is email', function () {
    expect(Email::OBJECT_NAME)->toBe('email');
});

test('Email::classUrl returns /v2/emails', function () {
    expect(Email::classUrl())->toBe('/v2/emails');
});

test('Email::resourceUrl builds id path', function () {
    expect(Email::resourceUrl('em_abc'))->toBe('/v2/emails/em_abc');
});

test('Email::resourceUrl url-encodes the id', function () {
    expect(Email::resourceUrl('em_with spaces'))->toBe('/v2/emails/em_with+spaces');
});

test('instanceUrl returns resource path for email with id', function () {
    $email = new Email(['id' => 'em_xyz']);

    expect($email->instanceUrl())->toBe('/v2/emails/em_xyz');
});

test('instanceUrl throws when id is missing', function () {
    $email = new Email([]);

    $email->instanceUrl();
})->throws(\RuntimeException::class, "has no 'id'");

// ──────────────────────────────────────────────────
// Collection
// ──────────────────────────────────────────────────

test('Collection is countable', function () {
    $col = new Collection([
        'data' => [['id' => '1'], ['id' => '2']],
        'next_page_url' => null,
        'previous_page_url' => null,
    ]);

    expect($col)->toHaveCount(2);
});

test('Collection hasMore checks next_page_url', function () {
    $with = new Collection(['data' => [], 'next_page_url' => '/v2/emails?page=2', 'previous_page_url' => null]);
    $without = new Collection(['data' => [], 'next_page_url' => null, 'previous_page_url' => null]);

    expect($with->hasMore())->toBeTrue()
        ->and($without->hasMore())->toBeFalse();
});

test('Collection is iterable', function () {
    $col = new Collection([
        'data' => [new EmailitObject(['id' => 'a']), new EmailitObject(['id' => 'b'])],
        'next_page_url' => null,
        'previous_page_url' => null,
    ]);

    $ids = [];
    foreach ($col as $item) {
        $ids[] = $item->id;
    }

    expect($ids)->toBe(['a', 'b']);
});

// ──────────────────────────────────────────────────
// Util::convertToEmailitObject
// ──────────────────────────────────────────────────

test('Util converts email object to Email class', function () {
    $obj = Util::convertToEmailitObject(['object' => 'email', 'id' => 'em_test']);

    expect($obj)->toBeInstanceOf(Email::class)
        ->and($obj->id)->toBe('em_test');
});

test('Util converts list response to Collection with typed items', function () {
    $obj = Util::convertToEmailitObject([
        'data' => [
            ['object' => 'email', 'id' => 'em_1'],
            ['object' => 'email', 'id' => 'em_2'],
        ],
        'next_page_url' => null,
        'previous_page_url' => null,
    ]);

    expect($obj)->toBeInstanceOf(Collection::class)
        ->and($obj->getData()[0])->toBeInstanceOf(Email::class)
        ->and($obj->getData()[1])->toBeInstanceOf(Email::class);
});

test('Util returns EmailitObject for unknown object type', function () {
    $obj = Util::convertToEmailitObject(['object' => 'unknown_type', 'id' => 'x']);

    expect($obj)->toBeInstanceOf(EmailitObject::class)
        ->and(get_class($obj))->toBe(EmailitObject::class);
});

test('Util returns EmailitObject when no object field', function () {
    $obj = Util::convertToEmailitObject(['text' => 'hello', 'html' => '<p>hello</p>']);

    expect($obj)->toBeInstanceOf(EmailitObject::class)
        ->and($obj->text)->toBe('hello');
});

test('Util returns null for null input', function () {
    expect(Util::convertToEmailitObject(null))->toBeNull();
});

// ──────────────────────────────────────────────────
// All Resource OBJECT_NAME and URL generation
// ──────────────────────────────────────────────────

test('all resource classes define correct OBJECT_NAME', function () {
    expect(Domain::OBJECT_NAME)->toBe('domain')
        ->and(ApiKey::OBJECT_NAME)->toBe('api_key')
        ->and(Audience::OBJECT_NAME)->toBe('audience')
        ->and(Subscriber::OBJECT_NAME)->toBe('subscriber')
        ->and(Template::OBJECT_NAME)->toBe('template')
        ->and(Suppression::OBJECT_NAME)->toBe('suppression')
        ->and(EmailVerification::OBJECT_NAME)->toBe('email_verification')
        ->and(EmailVerificationList::OBJECT_NAME)->toBe('email_verification_list')
        ->and(Webhook::OBJECT_NAME)->toBe('webhook')
        ->and(Contact::OBJECT_NAME)->toBe('contact')
        ->and(Event::OBJECT_NAME)->toBe('event');
});

test('all resource classes extend ApiResource', function () {
    expect(new Domain())->toBeInstanceOf(ApiResource::class)
        ->and(new ApiKey())->toBeInstanceOf(ApiResource::class)
        ->and(new Audience())->toBeInstanceOf(ApiResource::class)
        ->and(new Subscriber())->toBeInstanceOf(ApiResource::class)
        ->and(new Template())->toBeInstanceOf(ApiResource::class)
        ->and(new Suppression())->toBeInstanceOf(ApiResource::class)
        ->and(new EmailVerification())->toBeInstanceOf(ApiResource::class)
        ->and(new EmailVerificationList())->toBeInstanceOf(ApiResource::class)
        ->and(new Webhook())->toBeInstanceOf(ApiResource::class)
        ->and(new Contact())->toBeInstanceOf(ApiResource::class)
        ->and(new Event())->toBeInstanceOf(ApiResource::class);
});

test('Domain::classUrl returns /v2/domains', function () {
    expect(Domain::classUrl())->toBe('/v2/domains');
});

test('Webhook::resourceUrl builds correct path', function () {
    expect(Webhook::resourceUrl('wh_abc'))->toBe('/v2/webhooks/wh_abc');
});

test('Contact instanceUrl returns correct path', function () {
    $contact = new Contact(['id' => 'con_xyz']);
    expect($contact->instanceUrl())->toBe('/v2/contacts/con_xyz');
});

test('Util converts all known object types', function () {
    $types = [
        ['object' => 'domain', 'class' => Domain::class],
        ['object' => 'api_key', 'class' => ApiKey::class],
        ['object' => 'audience', 'class' => Audience::class],
        ['object' => 'subscriber', 'class' => Subscriber::class],
        ['object' => 'template', 'class' => Template::class],
        ['object' => 'suppression', 'class' => Suppression::class],
        ['object' => 'email_verification', 'class' => EmailVerification::class],
        ['object' => 'email_verification_list', 'class' => EmailVerificationList::class],
        ['object' => 'webhook', 'class' => Webhook::class],
        ['object' => 'contact', 'class' => Contact::class],
        ['object' => 'event', 'class' => Event::class],
    ];

    foreach ($types as $type) {
        $obj = Util::convertToEmailitObject(['object' => $type['object'], 'id' => 'test']);
        expect($obj)->toBeInstanceOf($type['class'], "Failed for object type: {$type['object']}");
    }
});
