<?php

namespace Emailit;

/**
 * @property string $id
 * @property string $object
 * @property string $email
 * @property string|null $type
 * @property string|null $reason
 * @property string|null $timestamp
 * @property string|null $keep_until
 * @property bool|null $deleted
 */
class Suppression extends ApiResource
{
    const OBJECT_NAME = 'suppression';
}
