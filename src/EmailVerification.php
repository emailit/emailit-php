<?php

namespace Emailit;

/**
 * @property string $id
 * @property string|null $object
 * @property string $email
 * @property string|null $status
 * @property float|null $score
 * @property string|null $risk
 * @property string|null $result
 * @property string|null $mode
 * @property array|null $checks
 * @property array|null $address
 * @property string|null $did_you_mean
 * @property array|null $mx_records
 */
class EmailVerification extends ApiResource
{
    const OBJECT_NAME = 'email_verification';
}
