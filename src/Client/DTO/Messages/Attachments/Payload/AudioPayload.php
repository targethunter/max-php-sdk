<?php

namespace TH\MAX\Client\DTO\Messages\Attachments\Payload;

use TH\MAX\DTO\BaseDTO;

class AudioPayload extends BaseDTO
{
    /**
     * Токен — уникальный ID загруженного медиафайла
     */
    public string $token;
}