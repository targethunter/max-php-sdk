<?php

namespace TH\MAX\Client\DTO\Messages\Attachments\Payload;

use TH\MAX\DTO\BaseDTO;

class StickerPayload extends BaseDTO
{
    /**
     * Код стикера
     */
    public string $code;
}