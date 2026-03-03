<?php

use Emailit\EmailitClient;

class Emailit
{
    public const VERSION = '1.0.0';

    /**
     * Creates a new Emailit client with the given API key.
     */
    public static function client(string $apiKey): EmailitClient
    {
        return new EmailitClient($apiKey);
    }
}
