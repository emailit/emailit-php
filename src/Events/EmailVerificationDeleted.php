<?php

namespace Emailit\Events;

class EmailVerificationDeleted extends WebhookEvent
{
    const EVENT_TYPE = 'email_verification.deleted';
}
