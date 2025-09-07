<?php

namespace TH\MAX\Client\DTO\Subscriptions;

use TH\MAX\Client\DTO\Messages\Message;
use TH\MAX\DTO\BaseDTO;

class Update extends BaseDTO
{
    public string $update_type;

    public int $timestamp;

    public Message $message;

    public ?string $user_locale;
}