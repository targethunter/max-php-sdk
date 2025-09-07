<?php

namespace TH\MAX\Client\DTO\Messages;

use TH\MAX\DTO\BaseDTO;

class MessageBody extends BaseDTO
{
    public string $mid;

    public int $seq;

    public ?string $text;

    public ?array $attachments;

    public ?array $markup;
}