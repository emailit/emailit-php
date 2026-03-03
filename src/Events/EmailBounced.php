<?php

namespace Emailit\Events;

class EmailBounced extends WebhookEvent
{
    const EVENT_TYPE = 'email.bounced';
}
