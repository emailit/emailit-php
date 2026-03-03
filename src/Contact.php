<?php

namespace Emailit;

/**
 * @property string $id
 * @property string $object
 * @property string $email
 * @property string|null $first_name
 * @property string|null $last_name
 * @property array|null $custom_fields
 * @property bool|null $unsubscribed
 * @property array|null $audiences
 * @property string $created_at
 * @property string|null $updated_at
 * @property bool|null $deleted
 */
class Contact extends ApiResource
{
    const OBJECT_NAME = 'contact';
}
