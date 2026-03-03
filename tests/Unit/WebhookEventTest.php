<?php

use Emailit\Events\WebhookEvent;
use Emailit\Events\EmailAccepted;
use Emailit\Events\EmailScheduled;
use Emailit\Events\EmailDelivered;
use Emailit\Events\EmailBounced;
use Emailit\Events\EmailAttempted;
use Emailit\Events\EmailFailed;
use Emailit\Events\EmailRejected;
use Emailit\Events\EmailSuppressed;
use Emailit\Events\EmailReceived;
use Emailit\Events\EmailComplained;
use Emailit\Events\EmailClicked;
use Emailit\Events\EmailLoaded;
use Emailit\Events\DomainCreated;
use Emailit\Events\DomainUpdated;
use Emailit\Events\DomainDeleted;
use Emailit\Events\AudienceCreated;
use Emailit\Events\AudienceUpdated;
use Emailit\Events\AudienceDeleted;
use Emailit\Events\SubscriberCreated;
use Emailit\Events\SubscriberUpdated;
use Emailit\Events\SubscriberDeleted;
use Emailit\Events\ContactCreated;
use Emailit\Events\ContactUpdated;
use Emailit\Events\ContactDeleted;
use Emailit\Events\TemplateCreated;
use Emailit\Events\TemplateUpdated;
use Emailit\Events\TemplateDeleted;
use Emailit\Events\SuppressionCreated;
use Emailit\Events\SuppressionUpdated;
use Emailit\Events\SuppressionDeleted;
use Emailit\Events\EmailVerificationCreated;
use Emailit\Events\EmailVerificationUpdated;
use Emailit\Events\EmailVerificationDeleted;
use Emailit\Events\EmailVerificationListCreated;
use Emailit\Events\EmailVerificationListUpdated;
use Emailit\Events\EmailVerificationListDeleted;

// ──────────────────────────────────────────────────
// constructFrom() dispatches to correct class
// ──────────────────────────────────────────────────

test('constructFrom returns typed event for all known event types', function () {
    $types = [
        'email.accepted' => EmailAccepted::class,
        'email.scheduled' => EmailScheduled::class,
        'email.delivered' => EmailDelivered::class,
        'email.bounced' => EmailBounced::class,
        'email.attempted' => EmailAttempted::class,
        'email.failed' => EmailFailed::class,
        'email.rejected' => EmailRejected::class,
        'email.suppressed' => EmailSuppressed::class,
        'email.received' => EmailReceived::class,
        'email.complained' => EmailComplained::class,
        'email.clicked' => EmailClicked::class,
        'email.loaded' => EmailLoaded::class,
        'domain.created' => DomainCreated::class,
        'domain.updated' => DomainUpdated::class,
        'domain.deleted' => DomainDeleted::class,
        'audience.created' => AudienceCreated::class,
        'audience.updated' => AudienceUpdated::class,
        'audience.deleted' => AudienceDeleted::class,
        'subscriber.created' => SubscriberCreated::class,
        'subscriber.updated' => SubscriberUpdated::class,
        'subscriber.deleted' => SubscriberDeleted::class,
        'contact.created' => ContactCreated::class,
        'contact.updated' => ContactUpdated::class,
        'contact.deleted' => ContactDeleted::class,
        'template.created' => TemplateCreated::class,
        'template.updated' => TemplateUpdated::class,
        'template.deleted' => TemplateDeleted::class,
        'suppression.created' => SuppressionCreated::class,
        'suppression.updated' => SuppressionUpdated::class,
        'suppression.deleted' => SuppressionDeleted::class,
        'email_verification.created' => EmailVerificationCreated::class,
        'email_verification.updated' => EmailVerificationUpdated::class,
        'email_verification.deleted' => EmailVerificationDeleted::class,
        'email_verification_list.created' => EmailVerificationListCreated::class,
        'email_verification_list.updated' => EmailVerificationListUpdated::class,
        'email_verification_list.deleted' => EmailVerificationListDeleted::class,
    ];

    foreach ($types as $eventType => $expectedClass) {
        $event = WebhookEvent::constructFrom([
            'event_id' => 'evt_test',
            'type' => $eventType,
            'data' => ['object' => ['id' => 'test']],
        ]);

        expect($event)->toBeInstanceOf($expectedClass, "Failed for type: {$eventType}")
            ->and($event)->toBeInstanceOf(WebhookEvent::class);
    }
});

// ──────────────────────────────────────────────────
// constructFrom() with unknown type
// ──────────────────────────────────────────────────

test('constructFrom returns generic WebhookEvent for unknown type', function () {
    $event = WebhookEvent::constructFrom([
        'event_id' => 'evt_test',
        'type' => 'unknown.event',
        'data' => ['object' => ['id' => 'test']],
    ]);

    expect($event)->toBeInstanceOf(WebhookEvent::class)
        ->and(get_class($event))->toBe(WebhookEvent::class)
        ->and($event->type)->toBe('unknown.event');
});

// ──────────────────────────────────────────────────
// Event property access
// ──────────────────────────────────────────────────

test('webhook event exposes properties', function () {
    $event = WebhookEvent::constructFrom([
        'event_id' => 'evt_abc123',
        'type' => 'email.delivered',
        'data' => [
            'object' => [
                'id' => 'em_xyz',
                'status' => 'delivered',
            ],
        ],
    ]);

    expect($event)->toBeInstanceOf(EmailDelivered::class)
        ->and($event->event_id)->toBe('evt_abc123')
        ->and($event->type)->toBe('email.delivered')
        ->and($event->data)->toBeArray();
});

// ──────────────────────────────────────────────────
// getEventData()
// ──────────────────────────────────────────────────

test('getEventData returns the nested object', function () {
    $event = WebhookEvent::constructFrom([
        'event_id' => 'evt_test',
        'type' => 'email.delivered',
        'data' => [
            'object' => [
                'id' => 'em_xyz',
                'status' => 'delivered',
            ],
        ],
    ]);

    expect($event->getEventData())->toBe(['id' => 'em_xyz', 'status' => 'delivered']);
});

test('getEventData falls back to data when object key missing', function () {
    $event = WebhookEvent::constructFrom([
        'event_id' => 'evt_test',
        'type' => 'email.delivered',
        'data' => ['id' => 'em_xyz'],
    ]);

    expect($event->getEventData())->toBe(['id' => 'em_xyz']);
});

test('getEventData returns null when data is missing', function () {
    $event = WebhookEvent::constructFrom([
        'event_id' => 'evt_test',
        'type' => 'email.delivered',
    ]);

    expect($event->getEventData())->toBeNull();
});

// ──────────────────────────────────────────────────
// EVENT_TYPE constants
// ──────────────────────────────────────────────────

test('all event classes define correct EVENT_TYPE constant', function () {
    expect(EmailAccepted::EVENT_TYPE)->toBe('email.accepted')
        ->and(EmailDelivered::EVENT_TYPE)->toBe('email.delivered')
        ->and(EmailBounced::EVENT_TYPE)->toBe('email.bounced')
        ->and(DomainCreated::EVENT_TYPE)->toBe('domain.created')
        ->and(AudienceDeleted::EVENT_TYPE)->toBe('audience.deleted')
        ->and(SubscriberUpdated::EVENT_TYPE)->toBe('subscriber.updated')
        ->and(ContactCreated::EVENT_TYPE)->toBe('contact.created')
        ->and(TemplateDeleted::EVENT_TYPE)->toBe('template.deleted')
        ->and(SuppressionCreated::EVENT_TYPE)->toBe('suppression.created')
        ->and(EmailVerificationCreated::EVENT_TYPE)->toBe('email_verification.created')
        ->and(EmailVerificationListDeleted::EVENT_TYPE)->toBe('email_verification_list.deleted');
});

// ──────────────────────────────────────────────────
// All event classes extend WebhookEvent
// ──────────────────────────────────────────────────

test('all event classes extend WebhookEvent', function () {
    $classes = [
        EmailAccepted::class, EmailScheduled::class, EmailDelivered::class,
        EmailBounced::class, EmailAttempted::class, EmailFailed::class,
        EmailRejected::class, EmailSuppressed::class, EmailReceived::class,
        EmailComplained::class, EmailClicked::class, EmailLoaded::class,
        DomainCreated::class, DomainUpdated::class, DomainDeleted::class,
        AudienceCreated::class, AudienceUpdated::class, AudienceDeleted::class,
        SubscriberCreated::class, SubscriberUpdated::class, SubscriberDeleted::class,
        ContactCreated::class, ContactUpdated::class, ContactDeleted::class,
        TemplateCreated::class, TemplateUpdated::class, TemplateDeleted::class,
        SuppressionCreated::class, SuppressionUpdated::class, SuppressionDeleted::class,
        EmailVerificationCreated::class, EmailVerificationUpdated::class, EmailVerificationDeleted::class,
        EmailVerificationListCreated::class, EmailVerificationListUpdated::class, EmailVerificationListDeleted::class,
    ];

    foreach ($classes as $class) {
        expect(new $class())->toBeInstanceOf(WebhookEvent::class, "Failed for: {$class}");
    }
});

// ──────────────────────────────────────────────────
// WebhookEvent is JSON serializable / ArrayAccess
// ──────────────────────────────────────────────────

test('webhook event supports ArrayAccess and JSON serialization', function () {
    $payload = [
        'event_id' => 'evt_test',
        'type' => 'email.delivered',
        'data' => ['object' => ['id' => 'em_1']],
    ];

    $event = WebhookEvent::constructFrom($payload);

    expect($event['event_id'])->toBe('evt_test')
        ->and($event['type'])->toBe('email.delivered')
        ->and(json_encode($event))->toBe(json_encode($payload));
});
