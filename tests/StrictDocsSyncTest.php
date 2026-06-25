<?php

declare(strict_types=1);

namespace TH\MAX\Tests;

use PHPUnit\Framework\TestCase;
use TH\MAX\Client\Modules\Bots\Bots;
use TH\MAX\Client\Modules\Chats\Chats;
use TH\MAX\Config\UploadTypes;

class StrictDocsSyncTest extends TestCase
{
    public function testUnsupportedMethodsAreRemovedFromPublicModules(): void
    {
        $this->assertFalse(method_exists(Chats::class, 'getAll'));
        $this->assertFalse(method_exists(Chats::class, 'delete'));
        $this->assertFalse(method_exists(Bots::class, 'update'));
    }

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
}
