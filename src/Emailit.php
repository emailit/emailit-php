<?php

use Emailit\EmailitClient;

class Emailit
{
    public const VERSION = '2.0.1';

    /**
     * Creates a new Emailit client with the given API key.
     */
    public static function client(string $apiKey): EmailitClient
    {
        return new EmailitClient($apiKey);
    }
}
