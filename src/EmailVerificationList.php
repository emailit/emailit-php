<?php

namespace Emailit;

/**
 * @property string $id
 * @property string $object
 * @property string $name
 * @property string $status
 * @property array|null $stats
 * @property string $created_at
 * @property string|null $updated_at
 */
class EmailVerificationList extends ApiResource
{
    const OBJECT_NAME = 'email_verification_list';
}
