<?php

namespace Emailit\Events;

use Emailit\EmailitObject;

/**
 * Base class for all webhook event types.
 *
 * @property string $event_id
 * @property string $type
 * @property array $data
 */
class WebhookEvent extends EmailitObject
{
    const EVENT_TYPE = '';

    /** @var array<string, class-string<WebhookEvent>> */
    private static array $eventMap = [];

    private static bool $mapInitialized = false;

    private static function initMap(): void
    {
        if (self::$mapInitialized) {
            return;
        }

        self::$eventMap = [
            EmailAccepted::EVENT_TYPE => EmailAccepted::class,
            EmailScheduled::EVENT_TYPE => EmailScheduled::class,
            EmailDelivered::EVENT_TYPE => EmailDelivered::class,
            EmailBounced::EVENT_TYPE => EmailBounced::class,
            EmailAttempted::EVENT_TYPE => EmailAttempted::class,
            EmailFailed::EVENT_TYPE => EmailFailed::class,
            EmailRejected::EVENT_TYPE => EmailRejected::class,
            EmailSuppressed::EVENT_TYPE => EmailSuppressed::class,
            EmailReceived::EVENT_TYPE => EmailReceived::class,
            EmailComplained::EVENT_TYPE => EmailComplained::class,
            EmailClicked::EVENT_TYPE => EmailClicked::class,
            EmailLoaded::EVENT_TYPE => EmailLoaded::class,
            DomainCreated::EVENT_TYPE => DomainCreated::class,
            DomainUpdated::EVENT_TYPE => DomainUpdated::class,
            DomainDeleted::EVENT_TYPE => DomainDeleted::class,
            AudienceCreated::EVENT_TYPE => AudienceCreated::class,
            AudienceUpdated::EVENT_TYPE => AudienceUpdated::class,
            AudienceDeleted::EVENT_TYPE => AudienceDeleted::class,
            SubscriberCreated::EVENT_TYPE => SubscriberCreated::class,
            SubscriberUpdated::EVENT_TYPE => SubscriberUpdated::class,
            SubscriberDeleted::EVENT_TYPE => SubscriberDeleted::class,
            ContactCreated::EVENT_TYPE => ContactCreated::class,
            ContactUpdated::EVENT_TYPE => ContactUpdated::class,
            ContactDeleted::EVENT_TYPE => ContactDeleted::class,
            TemplateCreated::EVENT_TYPE => TemplateCreated::class,
            TemplateUpdated::EVENT_TYPE => TemplateUpdated::class,
            TemplateDeleted::EVENT_TYPE => TemplateDeleted::class,
            SuppressionCreated::EVENT_TYPE => SuppressionCreated::class,
            SuppressionUpdated::EVENT_TYPE => SuppressionUpdated::class,
            SuppressionDeleted::EVENT_TYPE => SuppressionDeleted::class,
            EmailVerificationCreated::EVENT_TYPE => EmailVerificationCreated::class,
            EmailVerificationUpdated::EVENT_TYPE => EmailVerificationUpdated::class,
            EmailVerificationDeleted::EVENT_TYPE => EmailVerificationDeleted::class,
            EmailVerificationListCreated::EVENT_TYPE => EmailVerificationListCreated::class,
            EmailVerificationListUpdated::EVENT_TYPE => EmailVerificationListUpdated::class,
            EmailVerificationListDeleted::EVENT_TYPE => EmailVerificationListDeleted::class,
        ];

        self::$mapInitialized = true;
    }

    /**
     * Construct a typed WebhookEvent from a raw payload array.
     *
     * Returns the specific event subclass if the type is recognized,
     * or a generic WebhookEvent otherwise.
     */
    public static function constructFrom(array $payload): static|self
    {
        self::initMap();

        $type = $payload['type'] ?? null;

        if ($type !== null && isset(self::$eventMap[$type])) {
            return new self::$eventMap[$type]($payload);
        }

        return new self($payload);
    }

    /**
     * Get the event data object.
     */
    public function getEventData(): ?array
    {
        return $this->_values['data']['object'] ?? $this->_values['data'] ?? null;
    }
}
