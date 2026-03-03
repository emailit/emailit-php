<?php

namespace Emailit\Util;

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

class ObjectTypes
{
    /** @var array<string, class-string> */
    private static array $mapping = [
        Email::OBJECT_NAME => Email::class,
        Domain::OBJECT_NAME => Domain::class,
        ApiKey::OBJECT_NAME => ApiKey::class,
        Audience::OBJECT_NAME => Audience::class,
        Subscriber::OBJECT_NAME => Subscriber::class,
        Template::OBJECT_NAME => Template::class,
        Suppression::OBJECT_NAME => Suppression::class,
        EmailVerification::OBJECT_NAME => EmailVerification::class,
        EmailVerificationList::OBJECT_NAME => EmailVerificationList::class,
        Webhook::OBJECT_NAME => Webhook::class,
        Contact::OBJECT_NAME => Contact::class,
        Event::OBJECT_NAME => Event::class,
    ];

    public static function mapping(): array
    {
        return self::$mapping;
    }
}
