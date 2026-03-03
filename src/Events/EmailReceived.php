<?php

namespace Emailit\Events;

class EmailReceived extends WebhookEvent
{
    const EVENT_TYPE = 'email.received';
}
