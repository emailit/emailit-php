# Emailit PHP

[![Tests](https://img.shields.io/github/actions/workflow/status/emailit/emailit-php/tests.yml?label=tests&style=for-the-badge&labelColor=111827)](https://github.com/emailit/emailit-php/actions)
[![Packagist Version](https://img.shields.io/packagist/v/emailit/emailit-php?style=for-the-badge&labelColor=111827)](https://packagist.org/packages/emailit/emailit-php)
[![License](https://img.shields.io/github/license/emailit/emailit-php?style=for-the-badge&labelColor=111827)](https://github.com/emailit/emailit-php/blob/main/LICENSE)

The official PHP SDK for the [Emailit](https://emailit.com) Email API.

## Requirements

- PHP 8.1+
- [Guzzle](https://github.com/guzzle/guzzle) 7.5+

## Installation

```bash
composer require emailit/emailit-php
```

## Getting Started

```php
require 'vendor/autoload.php';

$emailit = Emailit::client('your_api_key');

$email = $emailit->emails->send([
    'from'    => 'hello@yourdomain.com',
    'to'      => ['user@example.com'],
    'subject' => 'Hello from Emailit',
    'html'    => '<h1>Welcome!</h1><p>Thanks for signing up.</p>',
]);

echo $email->id;     // em_abc123...
echo $email->status; // pending
```

All service methods return typed resource objects (`Email`, `Domain`, `Contact`, etc.) with direct property access -- just like the Stripe SDK.

## Available Services

| Service | Property | Description |
|---------|----------|-------------|
| Emails | `$emailit->emails` | Send, list, get, cancel, retry emails |
| Domains | `$emailit->domains` | Create, verify, list, manage sending domains |
| API Keys | `$emailit->apiKeys` | Create, list, manage API keys |
| Audiences | `$emailit->audiences` | Create, list, manage audiences |
| Subscribers | `$emailit->subscribers` | Add, list, manage subscribers in audiences |
| Templates | `$emailit->templates` | Create, list, publish email templates |
| Suppressions | `$emailit->suppressions` | Create, list, manage suppressed addresses |
| Email Verifications | `$emailit->emailVerifications` | Verify email addresses |
| Email Verification Lists | `$emailit->emailVerificationLists` | Create, list, get results, export |
| Webhooks | `$emailit->webhooks` | Create, list, manage webhooks |
| Contacts | `$emailit->contacts` | Create, list, manage contacts |
| Events | `$emailit->events` | List and retrieve events |

## Usage

### Emails

#### Send an email

```php
$email = $emailit->emails->send([
    'from'    => 'hello@yourdomain.com',
    'to'      => ['user@example.com'],
    'subject' => 'Hello from Emailit',
    'html'    => '<h1>Welcome!</h1>',
]);

echo $email->id;
echo $email->status;
```

#### Send with a template

```php
$email = $emailit->emails->send([
    'from'      => 'hello@yourdomain.com',
    'to'        => 'user@example.com',
    'template'  => 'welcome_email',
    'variables' => [
        'name'    => 'John Doe',
        'company' => 'Acme Inc',
    ],
]);
```

#### Send with attachments

```php
$email = $emailit->emails->send([
    'from'        => 'invoices@yourdomain.com',
    'to'          => 'customer@example.com',
    'subject'     => 'Your Invoice #12345',
    'html'        => '<p>Please find your invoice attached.</p>',
    'attachments' => [
        [
            'filename'     => 'invoice.pdf',
            'content'      => base64_encode(file_get_contents('invoice.pdf')),
            'content_type' => 'application/pdf',
        ],
    ],
]);
```

#### Schedule an email

```php
$email = $emailit->emails->send([
    'from'         => 'reminders@yourdomain.com',
    'to'           => 'user@example.com',
    'subject'      => 'Appointment Reminder',
    'html'         => '<p>Your appointment is tomorrow at 2 PM.</p>',
    'scheduled_at' => '2026-01-10T09:00:00Z',
]);

echo $email->status;       // scheduled
echo $email->scheduled_at; // 2026-01-10T09:00:00Z
```

#### List emails

```php
$emails = $emailit->emails->list(['page' => 1, 'limit' => 10]);

foreach ($emails as $email) {
    echo $email->id . ' — ' . $email->status . "\n";
}

if ($emails->hasMore()) {
    // fetch next page
}
```

#### Cancel / Retry

```php
$emailit->emails->cancel('em_abc123');
$emailit->emails->retry('em_abc123');
```

---

### Domains

```php
// Create a domain
$domain = $emailit->domains->create([
    'name' => 'example.com',
    'track_loads' => true,
    'track_clicks' => true,
]);
echo $domain->id;

// Verify DNS
$domain = $emailit->domains->verify('sd_123');

// List all domains
$domains = $emailit->domains->list();

// Get a domain
$domain = $emailit->domains->get('sd_123');

// Update a domain
$domain = $emailit->domains->update('sd_123', ['track_clicks' => false]);

// Delete a domain
$emailit->domains->delete('sd_123');
```

---

### API Keys

```php
// Create an API key
$key = $emailit->apiKeys->create([
    'name' => 'Production Key',
    'scope' => 'full',
]);
echo $key->key; // only available on create

// List all API keys
$keys = $emailit->apiKeys->list();

// Get an API key
$key = $emailit->apiKeys->get('ak_123');

// Update an API key
$emailit->apiKeys->update('ak_123', ['name' => 'Renamed Key']);

// Delete an API key
$emailit->apiKeys->delete('ak_123');
```

---

### Audiences

```php
// Create an audience
$audience = $emailit->audiences->create(['name' => 'Newsletter']);
echo $audience->id;
echo $audience->token;

// List audiences
$audiences = $emailit->audiences->list();

// Get an audience
$audience = $emailit->audiences->get('aud_123');

// Update an audience
$emailit->audiences->update('aud_123', ['name' => 'Updated Newsletter']);

// Delete an audience
$emailit->audiences->delete('aud_123');
```

---

### Subscribers

Subscribers belong to an audience, so the audience ID is always the first argument.

```php
// Add a subscriber
$subscriber = $emailit->subscribers->create('aud_123', [
    'email' => 'user@example.com',
    'first_name' => 'John',
    'last_name' => 'Doe',
]);

// List subscribers in an audience
$subscribers = $emailit->subscribers->list('aud_123');

// Get a subscriber
$subscriber = $emailit->subscribers->get('aud_123', 'sub_456');

// Update a subscriber
$emailit->subscribers->update('aud_123', 'sub_456', [
    'first_name' => 'Jane',
]);

// Delete a subscriber
$emailit->subscribers->delete('aud_123', 'sub_456');
```

---

### Templates

```php
// Create a template
$result = $emailit->templates->create([
    'name' => 'Welcome',
    'subject' => 'Welcome!',
    'html' => '<h1>Hi {{name}}</h1>',
]);

// List templates
$templates = $emailit->templates->list();

// Get a template
$template = $emailit->templates->get('tem_123');

// Update a template
$emailit->templates->update('tem_123', ['subject' => 'New Subject']);

// Publish a template
$emailit->templates->publish('tem_123');

// Delete a template
$emailit->templates->delete('tem_123');
```

---

### Suppressions

```php
// Create a suppression
$suppression = $emailit->suppressions->create([
    'email' => 'spam@example.com',
    'type' => 'hard_bounce',
    'reason' => 'Manual suppression',
]);

// List suppressions
$suppressions = $emailit->suppressions->list();

// Get a suppression
$suppression = $emailit->suppressions->get('sup_123');

// Update a suppression
$emailit->suppressions->update('sup_123', ['reason' => 'Updated']);

// Delete a suppression
$emailit->suppressions->delete('sup_123');
```

---

### Email Verifications

```php
$result = $emailit->emailVerifications->verify([
    'email' => 'test@example.com',
]);

echo $result->status; // valid
echo $result->score;  // 0.95
echo $result->risk;   // low
```

---

### Email Verification Lists

```php
// Create a verification list
$list = $emailit->emailVerificationLists->create([
    'name' => 'Marketing List Q1',
    'emails' => [
        'user1@example.com',
        'user2@example.com',
        'user3@example.com',
    ],
]);
echo $list->id;     // evl_abc123...
echo $list->status; // pending

// List all verification lists
$lists = $emailit->emailVerificationLists->list();

// Get a verification list
$list = $emailit->emailVerificationLists->get('evl_abc123');
echo $list->stats['successful_verifications'];

// Get verification results
$results = $emailit->emailVerificationLists->results('evl_abc123', ['page' => 1, 'limit' => 50]);

foreach ($results as $result) {
    echo $result->email . ' — ' . $result->result . "\n";
}

// Export results as XLSX
$response = $emailit->emailVerificationLists->export('evl_abc123');
file_put_contents('results.xlsx', $response->body);
```

---

### Webhooks

```php
// Create a webhook
$webhook = $emailit->webhooks->create([
    'name' => 'My Webhook',
    'url' => 'https://example.com/hook',
    'all_events' => true,
    'enabled' => true,
]);
echo $webhook->id;

// List webhooks
$webhooks = $emailit->webhooks->list();

// Get a webhook
$webhook = $emailit->webhooks->get('wh_123');

// Update a webhook
$emailit->webhooks->update('wh_123', ['enabled' => false]);

// Delete a webhook
$emailit->webhooks->delete('wh_123');
```

---

### Contacts

```php
// Create a contact
$contact = $emailit->contacts->create([
    'email' => 'user@example.com',
    'first_name' => 'John',
    'last_name' => 'Doe',
]);
echo $contact->id;

// List contacts
$contacts = $emailit->contacts->list();

// Get a contact
$contact = $emailit->contacts->get('con_123');

// Update a contact
$emailit->contacts->update('con_123', ['first_name' => 'Jane']);

// Delete a contact
$emailit->contacts->delete('con_123');
```

---

### Events

```php
// List events
$events = $emailit->events->list(['type' => 'email.delivered']);

foreach ($events as $event) {
    echo $event->type . "\n";
}

// Get an event
$event = $emailit->events->get('evt_123');
echo $event->type;
echo $event->data['email_id'];
```

## Webhook Events

The SDK provides typed event classes for all Emailit webhook event types under the `Emailit\Events` namespace, plus a `WebhookSignature` class for verifying webhook request signatures.

### Verifying Webhook Signatures

```php
use Emailit\WebhookSignature;
use Emailit\Events\EmailDelivered;
use Emailit\Exceptions\ApiErrorException;

$rawBody = file_get_contents('php://input');
$signature = $_SERVER['HTTP_X_EMAILIT_SIGNATURE'];
$timestamp = $_SERVER['HTTP_X_EMAILIT_TIMESTAMP'];
$secret = 'your_webhook_signing_secret';

try {
    $event = WebhookSignature::verify($rawBody, $signature, $timestamp, $secret);

    // $event is automatically typed based on the event type
    echo $event->type;     // e.g. "email.delivered"
    echo $event->event_id; // e.g. "evt_abc123"

    // Access the event data
    $data = $event->getEventData();

    if ($event instanceof EmailDelivered) {
        // Handle delivered email
    }
} catch (ApiErrorException $e) {
    http_response_code(401);
    exit($e->getMessage());
}
```

You can disable replay protection by passing `tolerance: null`, or set a custom tolerance in seconds:

```php
// Skip replay check
$event = WebhookSignature::verify($rawBody, $signature, $timestamp, $secret, tolerance: null);

// Custom 10-minute tolerance
$event = WebhookSignature::verify($rawBody, $signature, $timestamp, $secret, tolerance: 600);
```

### Available Event Types

**Emails:** `email.accepted`, `email.scheduled`, `email.delivered`, `email.bounced`, `email.attempted`, `email.failed`, `email.rejected`, `email.suppressed`, `email.received`, `email.complained`, `email.clicked`, `email.loaded`

**Domains:** `domain.created`, `domain.updated`, `domain.deleted`

**Audiences:** `audience.created`, `audience.updated`, `audience.deleted`

**Subscribers:** `subscriber.created`, `subscriber.updated`, `subscriber.deleted`

**Contacts:** `contact.created`, `contact.updated`, `contact.deleted`

**Templates:** `template.created`, `template.updated`, `template.deleted`

**Suppressions:** `suppression.created`, `suppression.updated`, `suppression.deleted`

**Email Verifications:** `email_verification.created`, `email_verification.updated`, `email_verification.deleted`

**Email Verification Lists:** `email_verification_list.created`, `email_verification_list.updated`, `email_verification_list.deleted`

Each event type has a corresponding class under `Emailit\Events\` (e.g. `Emailit\Events\EmailDelivered`, `Emailit\Events\DomainCreated`). You can use `instanceof` checks or the `EVENT_TYPE` constant for routing:

```php
use Emailit\Events\EmailDelivered;
use Emailit\Events\EmailBounced;
use Emailit\Events\ContactCreated;

match (true) {
    $event instanceof EmailDelivered  => handleDelivered($event),
    $event instanceof EmailBounced    => handleBounce($event),
    $event instanceof ContactCreated  => handleNewContact($event),
    default                           => log("Unhandled: {$event->type}"),
};
```

## Error Handling

The SDK throws typed exceptions for API errors:

```php
use Emailit\Exceptions\AuthenticationException;
use Emailit\Exceptions\InvalidRequestException;
use Emailit\Exceptions\RateLimitException;
use Emailit\Exceptions\UnprocessableEntityException;
use Emailit\Exceptions\ApiConnectionException;
use Emailit\Exceptions\ApiErrorException;

try {
    $emailit->emails->send([...]);
} catch (AuthenticationException $e) {
    // Invalid API key (401)
} catch (InvalidRequestException $e) {
    // Bad request or not found (400, 404)
} catch (RateLimitException $e) {
    // Too many requests (429)
} catch (UnprocessableEntityException $e) {
    // Validation failed (422)
} catch (ApiConnectionException $e) {
    // Network error
} catch (ApiErrorException $e) {
    // Any other API error
    echo $e->getHttpStatus();
    echo $e->getHttpBody();
    print_r($e->getJsonBody());
}
```

## License

MIT — see [LICENSE](LICENSE) for details.
