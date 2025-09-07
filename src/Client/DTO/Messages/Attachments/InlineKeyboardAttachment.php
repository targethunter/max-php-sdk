<?php

namespace TH\MAX\Client\DTO\Messages\Attachments;

use TH\MAX\Client\DTO\Messages\Attachments\Payload\InlineKeyboardPayload;
use TH\MAX\DTO\BaseDTO;

class InlineKeyboardAttachment extends BaseDTO
{
    public string $type = 'inline_keyboard';

    public InlineKeyboardPayload $payload;
}