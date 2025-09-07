<?php

namespace TH\MAX\Client\DTO\Messages\Attachments\Buttons;

use TH\MAX\DTO\BaseDTO;

class CallbackButton extends BaseDTO
{
    public string $type = 'callback';

    /**
     * От 1 до 128 символов
     *
     * Видимый текст кнопки
     */
    public string $text;

    /**
     * До 1024 символов
     *
     * Токен кнопки
     */
    public string $payload;

    /**
     * По умолчанию: "default"
     * Enum: "positive" "negative" "default"
     * @see Intents
     *
     * Намерение кнопки. Влияет на отображение клиентом.
     */
    public ?string $intent = null;
}