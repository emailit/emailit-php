<?php

namespace Emailit\Util;

use Emailit\ApiResource;
use Emailit\Collection;
use Emailit\EmailitObject;

class Util
{
    /**
     * Convert a JSON-decoded API response into the appropriate EmailitObject subclass.
     */
    public static function convertToEmailitObject(?array $data): EmailitObject|Collection|null
    {
        if ($data === null) {
            return null;
        }

        if (isset($data['data']) && is_array($data['data']) && array_is_list($data['data'])) {
            $data['data'] = array_map(fn (array $item) => self::convertToEmailitObject($item), $data['data']);

            return new Collection($data);
        }

        $objectType = $data['object'] ?? null;
        $mapping = ObjectTypes::mapping();

        if ($objectType !== null && isset($mapping[$objectType])) {
            $class = $mapping[$objectType];
            return new $class($data);
        }

        return new EmailitObject($data);
    }
}
