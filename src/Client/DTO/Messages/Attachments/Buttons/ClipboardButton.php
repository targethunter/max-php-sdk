<?php

namespace TH\MAX\Client\DTO\Messages\Attachments\Buttons;

use TH\MAX\DTO\BaseDTO;

/**
 * @see https://dev.max.ru/docs-api/methods/POST/messages Inline keyboard buttons are sent through POST /messages.
 */
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
