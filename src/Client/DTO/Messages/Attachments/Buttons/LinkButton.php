<?php

namespace TH\MAX\Client\DTO\Messages\Attachments\Buttons;

use TH\MAX\DTO\BaseDTO;

class LinkButton extends BaseDTO
{
    public string $type = 'link';

    /**
     * От 1 до 128 символов
     *
     * Видимый текст кнопки
     */
    public string $text;

    /**
     * До 2048 символов
     */
    public string $url;
}