<?php

namespace Emailit\Events;

class EmailRejected extends WebhookEvent
{
    const EVENT_TYPE = 'email.rejected';
}
