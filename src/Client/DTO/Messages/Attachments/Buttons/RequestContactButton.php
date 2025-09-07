<?php

namespace TH\MAX\Client\DTO\Messages\Attachments\Buttons;

use TH\MAX\DTO\BaseDTO;

class RequestContactButton extends BaseDTO
{
    public string $type = 'request_contact';

    /**
     * От 1 до 128 символов
     *
     * Видимый текст кнопки
     */
    public string $text;
}