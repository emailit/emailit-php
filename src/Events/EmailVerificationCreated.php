<?php

namespace Emailit\Events;

class EmailVerificationCreated extends WebhookEvent
{
    const EVENT_TYPE = 'email_verification.created';
}
