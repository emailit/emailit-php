# Emailit PHP

[![Tests](https://img.shields.io/github/actions/workflow/status/emailit/emailit-php/tests.yml?branch=main?label=tests&style=for-the-badge&labelColor=111827)](https://github.com/emailit/emailit-php/actions)
[![Packagist Version](https://img.shields.io/packagist/v/emailit/emailit-php)](https://packagist.org/packages/emailit/emailit-php)
[![License](https://img.shields.io/packagist/l/emailit/emailit-php)](LICENSE)

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
