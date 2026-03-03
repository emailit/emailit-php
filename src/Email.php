<?php

namespace Emailit;

/**
 * @property string $id
 * @property string $object
 * @property string|null $type
 * @property string|null $token
 * @property string|null $message_id
 * @property string $from
 * @property string|array $to
 * @property array|null $cc
 * @property array|null $bcc
 * @property string|null $subject
 * @property string $status
 * @property int|null $size
 * @property string|null $scheduled_at
 * @property string $created_at
 * @property string|null $updated_at
 * @property array|null $tracking
 * @property array|null $meta
 * @property array|null $headers
 * @property array|null $body
 * @property string|null $raw
 * @property array|null $attachments
 * @property array|null $ids
 * @property string|null $original_id
 * @property string|null $message
 */
class Email extends ApiResource
{
    const OBJECT_NAME = 'email';
}
