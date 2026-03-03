<?php

namespace Emailit;

/**
 * @property string $id
 * @property string $object
 * @property string $name
 * @property string|null $scope
 * @property string|null $sending_domain_id
 * @property string|null $key
 * @property string|null $last_used_at
 * @property string $created_at
 * @property string|null $updated_at
 * @property bool|null $deleted
 */
class ApiKey extends ApiResource
{
    const OBJECT_NAME = 'api_key';
}
