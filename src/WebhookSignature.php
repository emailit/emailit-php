<?php

namespace Emailit;

use Emailit\Events\WebhookEvent;
use Emailit\Exceptions\ApiErrorException;

class WebhookSignature
{
    public const HEADER_SIGNATURE = 'x-emailit-signature';
    public const HEADER_TIMESTAMP = 'x-emailit-timestamp';

    /**
     * Default tolerance for replay attack protection (5 minutes).
     */
    public const DEFAULT_TOLERANCE = 300;

    /**
     * Verify a webhook signature and return the parsed event.
     *
     * @param string $rawBody     The raw request body (JSON string).
     * @param string $signature   The value of the x-emailit-signature header.
     * @param string $timestamp   The value of the x-emailit-timestamp header.
     * @param string $secret      Your webhook signing secret.
     * @param int|null $tolerance Maximum allowed age in seconds (null to skip replay check).
     *
     * @throws Exceptions\ApiErrorException If the signature is invalid or the request is too old.
     */
    public static function verify(
        string $rawBody,
        string $signature,
        string $timestamp,
        string $secret,
        ?int $tolerance = self::DEFAULT_TOLERANCE,
    ): WebhookEvent {
        if ($tolerance !== null) {
            $age = time() - (int) $timestamp;

            if ($age > $tolerance) {
                throw new ApiErrorException(
                    'Webhook timestamp is too old. The request may be a replay attack.',
                    httpStatus: 401,
                );
            }
        }

        $signedPayload = "{$timestamp}.{$rawBody}";
        $computed = hash_hmac('sha256', $signedPayload, $secret);

        if (!hash_equals($computed, $signature)) {
            throw new ApiErrorException(
                'Webhook signature verification failed.',
                httpStatus: 401,
            );
        }

        $payload = json_decode($rawBody, true);

        if (!is_array($payload)) {
            throw new ApiErrorException(
                'Invalid webhook payload: unable to decode JSON.',
                httpStatus: 400,
            );
        }

        return WebhookEvent::constructFrom($payload);
    }

    /**
     * Compute the expected signature for a given payload.
     *
     * Useful for testing or debugging.
     */
    public static function computeSignature(string $rawBody, string $timestamp, string $secret): string
    {
        $signedPayload = "{$timestamp}.{$rawBody}";

        return hash_hmac('sha256', $signedPayload, $secret);
    }
}
