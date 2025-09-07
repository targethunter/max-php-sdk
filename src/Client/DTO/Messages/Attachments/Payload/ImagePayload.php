<?php

namespace TH\MAX\Client\DTO\Messages\Attachments\Payload;

use TH\MAX\DTO\BaseDTO;

class ImagePayload extends BaseDTO
{
    /**
     * От 1 символа.
     *
     * Любой внешний URL изображения, которое вы хотите прикрепить
     */
    public ?string $url = null;

    /**
     * Токен существующего вложения
     */
    public ?string $token = null;

    /**
     * Токены, полученные после загрузки изображений
     */
    public ?array $photos = null;
}