<?php

declare(strict_types=1);

namespace TH\MAX\Tests;

use PHPUnit\Framework\TestCase;
use TH\MAX\Client\DTO\Messages\Attachments\Buttons\ClipboardButton;
use TH\MAX\Client\Modules\Bots\Bots;
use TH\MAX\Client\Modules\Chats\Chats;
use TH\MAX\Config\UploadTypes;

class StrictDocsSyncTest extends TestCase
{
    /**
     * @see https://dev.max.ru/docs-api/methods/GET/chats GET /chats is no longer supported.
     * @see https://dev.max.ru/docs-api/methods/GET/me Bots API documents GET /me, not PATCH /me.
     */
    public function testUnsupportedMethodsAreRemovedFromPublicModules(): void
    {
        $this->assertFalse(method_exists(Chats::class, 'getAll'));
        $this->assertFalse(method_exists(Chats::class, 'delete'));
        $this->assertFalse(method_exists(Bots::class, 'update'));
    }

    /**
     * @see https://dev.max.ru/docs-api/methods/POST/uploads
     */
    public function testUploadTypesExposeOnlyDocumentedTypes(): void
    {
        $reflection = new \ReflectionClass(UploadTypes::class);

        $this->assertSame([
            'IMAGE' => 'image',
            'VIDEO' => 'video',
            'AUDIO' => 'audio',
            'FILE' => 'file',
        ], $reflection->getConstants());

        $this->assertFalse($reflection->hasConstant('PHOTO'));
    }

    /**
     * @see https://dev.max.ru/docs-api/methods/POST/messages Inline keyboard buttons are sent through POST /messages.
     */
    public function testClipboardButtonSerializesToDocumentedShape(): void
    {
        $button = new ClipboardButton([
            'text' => 'Copy',
            'payload' => 'PROMO-123',
        ]);

        $this->assertSame([
            'type' => 'clipboard',
            'text' => 'Copy',
            'payload' => 'PROMO-123',
        ], $button->toArray());
    }
}
