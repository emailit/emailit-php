<?php

namespace Emailit;

/**
 * @property string $id
 * @property string $object
 * @property string $name
 * @property string|null $token
 * @property string $created_at
 * @property string|null $updated_at
 * @property bool|null $deleted
 */
class Audience extends ApiResource
{
    const OBJECT_NAME = 'audience';
}
