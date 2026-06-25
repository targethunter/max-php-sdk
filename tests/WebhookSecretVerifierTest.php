<?php

declare(strict_types=1);

namespace TH\MAX\Tests;

use PHPUnit\Framework\TestCase;
use TH\MAX\Webhook\WebhookSecretVerifier;

class WebhookSecretVerifierTest extends TestCase
{
    /**
     * @see https://dev.max.ru/docs-api/methods/POST/subscriptions
     */
    public function testVerifyAcceptsMatchingSecret(): void
    {
        $this->assertTrue(WebhookSecretVerifier::verify('expected-secret', 'expected-secret'));
    }

    /**
     * @see https://dev.max.ru/docs-api/methods/POST/subscriptions
     */
    public function testVerifyRejectsMismatchedSecret(): void
    {
        $this->assertFalse(WebhookSecretVerifier::verify('wrong-secret', 'expected-secret'));
    }

    /**
     * @see https://dev.max.ru/docs-api/methods/POST/subscriptions
     */
    public function testVerifyRejectsMissingHeader(): void
    {
        $this->assertFalse(WebhookSecretVerifier::verify(null, 'expected-secret'));
        $this->assertFalse(WebhookSecretVerifier::verify('', 'expected-secret'));
    }

    /**
     * @see https://dev.max.ru/docs-api/methods/POST/subscriptions
     */
    public function testVerifyRejectsEmptyExpectedSecret(): void
    {
        $this->assertFalse(WebhookSecretVerifier::verify('anything', ''));
    }

    /**
     * @see https://dev.max.ru/docs-api/methods/POST/subscriptions
     */
    public function testVerifyFromHeadersFindsMaxSecretHeaderCaseInsensitively(): void
    {
        $headers = [
            'x-max-bot-api-secret' => 'expected-secret',
        ];

        $this->assertTrue(WebhookSecretVerifier::verifyFromHeaders($headers, 'expected-secret'));
    }

    /**
     * @see https://dev.max.ru/docs-api/methods/POST/subscriptions
     */
    public function testVerifyFromHeadersAcceptsArrayHeaderValues(): void
    {
        $headers = [
            'X-Max-Bot-Api-Secret' => ['expected-secret'],
        ];

        $this->assertTrue(WebhookSecretVerifier::verifyFromHeaders($headers, 'expected-secret'));
    }
}
