<?php

declare(strict_types=1);

namespace ScanLyser\Webhooks;

class WebhookSignature
{
    /**
     * Verify a webhook signature against the request payload.
     *
     * The signature header value is expected in the format: sha256=<hex>
     */
    public static function verify(string $payload, string $signature, string $secret): bool
    {
        $expected = 'sha256='.hash_hmac('sha256', $payload, $secret);

        return hash_equals($expected, $signature);
    }
}
