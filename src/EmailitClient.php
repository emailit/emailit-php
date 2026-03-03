<?php

namespace Emailit;

use Emailit\Service\ApiKeyService;
use Emailit\Service\AudienceService;
use Emailit\Service\ContactService;
use Emailit\Service\CoreServiceFactory;
use Emailit\Service\DomainService;
use Emailit\Service\EmailService;
use Emailit\Service\EmailVerificationListService;
use Emailit\Service\EmailVerificationService;
use Emailit\Service\EventService;
use Emailit\Service\SubscriberService;
use Emailit\Service\SuppressionService;
use Emailit\Service\TemplateService;
use Emailit\Service\WebhookService;

class EmailitClient extends BaseEmailitClient
{
    private CoreServiceFactory $serviceFactory;

    /** @var array<string, object> */
    private array $serviceCache = [];

    public function __construct(string|array $config)
    {
        parent::__construct($config);
        $this->serviceFactory = new CoreServiceFactory($this);
    }

    public function emails(): EmailService
    {
        return $this->resolveService('emails');
    }

    public function domains(): DomainService
    {
        return $this->resolveService('domains');
    }

    public function apiKeys(): ApiKeyService
    {
        return $this->resolveService('apiKeys');
    }

    public function audiences(): AudienceService
    {
        return $this->resolveService('audiences');
    }

    public function subscribers(): SubscriberService
    {
        return $this->resolveService('subscribers');
    }

    public function templates(): TemplateService
    {
        return $this->resolveService('templates');
    }

    public function suppressions(): SuppressionService
    {
        return $this->resolveService('suppressions');
    }

    public function emailVerifications(): EmailVerificationService
    {
        return $this->resolveService('emailVerifications');
    }

    public function emailVerificationLists(): EmailVerificationListService
    {
        return $this->resolveService('emailVerificationLists');
    }

    public function webhooks(): WebhookService
    {
        return $this->resolveService('webhooks');
    }

    public function contacts(): ContactService
    {
        return $this->resolveService('contacts');
    }

    public function events(): EventService
    {
        return $this->resolveService('events');
    }

    /**
     * @template T
     * @param string $name
     * @return T
     */
    private function resolveService(string $name): mixed
    {
        if (!isset($this->serviceCache[$name])) {
            $this->serviceCache[$name] = $this->serviceFactory->getService($name);
        }

        return $this->serviceCache[$name];
    }
}
