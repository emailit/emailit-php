<?php

namespace Emailit\Service;

class CoreServiceFactory extends AbstractServiceFactory
{
    protected function getServiceMap(): array
    {
        return [
            'emails' => EmailService::class,
            'domains' => DomainService::class,
            'apiKeys' => ApiKeyService::class,
            'audiences' => AudienceService::class,
            'subscribers' => SubscriberService::class,
            'templates' => TemplateService::class,
            'suppressions' => SuppressionService::class,
            'emailVerifications' => EmailVerificationService::class,
            'emailVerificationLists' => EmailVerificationListService::class,
            'webhooks' => WebhookService::class,
            'contacts' => ContactService::class,
            'events' => EventService::class,
        ];
    }
}
