<?php

declare(strict_types=1);

namespace TH\MAX\Webhook;

class WebhookSecretVerifier
{
    public const HEADER_NAME = 'X-Max-Bot-Api-Secret';

    public static function verify(?string $receivedSecret, string $expectedSecret): bool
    {
        if ($expectedSecret === '' || $receivedSecret === null || $receivedSecret === '') {
            return false;
        }

        return hash_equals($expectedSecret, $receivedSecret);
    }

    public static function verifyFromHeaders(array $headers, string $expectedSecret): bool
    {
        foreach ($headers as $name => $value) {
            if (strcasecmp((string)$name, self::HEADER_NAME) !== 0) {
                continue;
            }

            if (is_array($value)) {
                $value = reset($value);
            }

            return self::verify(is_string($value) ? $value : null, $expectedSecret);
        }

        return false;
    }
}
