<?php

namespace Emailit;

/**
 * @property string $id
 * @property string $object
 * @property string|null $audience_id
 * @property string|null $contact_id
 * @property string $email
 * @property string|null $first_name
 * @property string|null $last_name
 * @property array|null $custom_fields
 * @property bool|null $subscribed
 * @property string|null $subscribed_at
 * @property string|null $unsubscribed_at
 * @property string $created_at
 * @property string|null $updated_at
 * @property bool|null $deleted
 */
class Subscriber extends ApiResource
{
    const OBJECT_NAME = 'subscriber';
}
