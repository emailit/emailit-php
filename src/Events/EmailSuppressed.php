<?php

namespace Emailit\Events;

class EmailSuppressed extends WebhookEvent
{
    const EVENT_TYPE = 'email.suppressed';
}
