<?php

namespace TH\MAX\Client\DTO\Messages\Attachments\Payload;

use TH\MAX\DTO\BaseDTO;

class SharePayload extends BaseDTO
{
    /**
     * От 1 символа
     *
     * URL, прикрепленный к сообщению в качестве предпросмотра медиа
     */
    public ?string $url = null;

    /**
     * Токен вложения
     */
    public ?string $token = null;
}