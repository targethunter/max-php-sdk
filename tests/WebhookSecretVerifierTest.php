<?php

declare(strict_types=1);

namespace TH\MAX\Tests;

use PHPUnit\Framework\TestCase;
use TH\MAX\Webhook\WebhookSecretVerifier;

class WebhookSecretVerifierTest extends TestCase
{
    public function testVerifyAcceptsMatchingSecret(): void
    {
        $this->assertTrue(WebhookSecretVerifier::verify('expected-secret', 'expected-secret'));
    }

    public function testVerifyRejectsMismatchedSecret(): void
    {
        $this->assertFalse(WebhookSecretVerifier::verify('wrong-secret', 'expected-secret'));
    }

    public function testVerifyRejectsMissingHeader(): void
    {
        $this->assertFalse(WebhookSecretVerifier::verify(null, 'expected-secret'));
        $this->assertFalse(WebhookSecretVerifier::verify('', 'expected-secret'));
    }

    public function testVerifyRejectsEmptyExpectedSecret(): void
    {
        $this->assertFalse(WebhookSecretVerifier::verify('anything', ''));
    }

    public function testVerifyFromHeadersFindsMaxSecretHeaderCaseInsensitively(): void
    {
        $headers = [
            'x-max-bot-api-secret' => 'expected-secret',
        ];

        $this->assertTrue(WebhookSecretVerifier::verifyFromHeaders($headers, 'expected-secret'));
    }

    public function testVerifyFromHeadersAcceptsArrayHeaderValues(): void
    {
        $headers = [
            'X-Max-Bot-Api-Secret' => ['expected-secret'],
        ];

        $this->assertTrue(WebhookSecretVerifier::verifyFromHeaders($headers, 'expected-secret'));
    }
}
