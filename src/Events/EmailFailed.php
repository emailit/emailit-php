<?php

namespace Emailit\Events;

class EmailFailed extends WebhookEvent
{
    const EVENT_TYPE = 'email.failed';
}
