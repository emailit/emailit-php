<?php

namespace Emailit;

/**
 * @property string $id
 * @property string $object
 * @property string $name
 * @property string $url
 * @property bool|null $all_events
 * @property bool|null $enabled
 * @property array|null $events
 * @property string|null $last_used_at
 * @property string $created_at
 * @property string|null $updated_at
 * @property bool|null $deleted
 */
class Webhook extends ApiResource
{
    const OBJECT_NAME = 'webhook';
}
