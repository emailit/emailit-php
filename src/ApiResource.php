<?php

namespace Emailit;

class ApiResource extends EmailitObject
{
    const OBJECT_NAME = '';

    public static function classUrl(): string
    {
        $objectName = static::OBJECT_NAME;

        return "/v2/{$objectName}s";
    }

    public static function resourceUrl(string $id): string
    {
        return static::classUrl() . '/' . urlencode($id);
    }

    public function instanceUrl(): string
    {
        $id = $this['id'] ?? null;

        if ($id === null) {
            $class = static::class;
            throw new \RuntimeException(
                "Could not determine instance URL: {$class} has no 'id'."
            );
        }

        return static::resourceUrl($id);
    }
}
