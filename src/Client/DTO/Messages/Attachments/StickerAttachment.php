<?php

namespace TH\MAX\Client\DTO\Messages\Attachments;

use TH\MAX\Client\DTO\Messages\Attachments\Payload\StickerPayload;
use TH\MAX\DTO\BaseDTO;

class StickerAttachment extends BaseDTO
{
    public string $type = 'sticker';

    public StickerPayload $payload;
}