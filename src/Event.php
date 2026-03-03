<?php

namespace Emailit;

/**
 * @property string $id
 * @property string $object
 * @property string $type
 * @property array|null $data
 * @property string $created_at
 */
class Event extends ApiResource
{
    const OBJECT_NAME = 'event';
}
