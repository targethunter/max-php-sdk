<?php

namespace TH\MAX\Client\DTO\Messages\Attachments\Buttons;

use TH\MAX\DTO\BaseDTO;

class ClipboardButton extends BaseDTO
{
    public string $type = 'clipboard';

    /**
     * От 1 до 128 символов.
     *
     * Видимый текст кнопки.
     */
    public string $text;

    /**
     * Текст, который будет скопирован в буфер обмена.
     */
    public string $payload;
}
