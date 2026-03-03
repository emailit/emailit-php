<?php

namespace Emailit;

/**
 * @property string $id
 * @property string $object
 * @property string $name
 * @property string|null $alias
 * @property string|null $from
 * @property string|null $subject
 * @property string|null $reply_to
 * @property string|null $html
 * @property string|null $text
 * @property string|null $editor
 * @property string|null $published_at
 * @property string|null $preview_url
 * @property int|null $total_versions
 * @property array|null $versions
 * @property string|null $message
 * @property string $created_at
 * @property string|null $updated_at
 * @property bool|null $deleted
 */
class Template extends ApiResource
{
    const OBJECT_NAME = 'template';
}
