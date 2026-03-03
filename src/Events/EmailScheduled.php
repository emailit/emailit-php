<?php

namespace Emailit\Events;

class EmailScheduled extends WebhookEvent
{
    const EVENT_TYPE = 'email.scheduled';
}
