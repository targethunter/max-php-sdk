<?php

namespace TH\MAX\Client\DTO\Messages\Attachments\Payload;

use TH\MAX\DTO\BaseDTO;

class InlineKeyboardPayload extends BaseDTO
{
    /**
     * Двумерный массив кнопок
     */
    public array $buttons;
}