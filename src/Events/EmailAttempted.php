<?php

namespace Emailit\Events;

class EmailAttempted extends WebhookEvent
{
    const EVENT_TYPE = 'email.attempted';
}
