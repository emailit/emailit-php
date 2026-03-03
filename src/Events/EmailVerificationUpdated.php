<?php

namespace Emailit\Events;

class EmailVerificationUpdated extends WebhookEvent
{
    const EVENT_TYPE = 'email_verification.updated';
}
