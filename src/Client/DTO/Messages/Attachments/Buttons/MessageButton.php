<?php

namespace TH\MAX\Client\DTO\Messages\Attachments\Buttons;

use TH\MAX\DTO\BaseDTO;

class MessageButton extends BaseDTO
{
    public string $type = 'message';

    /**
     * От 1 до 128 символов
     *
     * Текст кнопки, который будет отправлен в чат от лица пользователя
     */
    public string $text;
}