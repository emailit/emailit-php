<?php

namespace Emailit\Events;

class EmailDelivered extends WebhookEvent
{
    const EVENT_TYPE = 'email.delivered';
}
