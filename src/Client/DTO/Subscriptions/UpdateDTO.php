<?php

namespace TH\MAX\Client\DTO\Subscriptions;

use TH\MAX\Client\DTO\Messages\MessageDTO;
use TH\MAX\DTO\BaseDTO;

class UpdateDTO extends BaseDTO
{
    public string $update_type;

    public int $timestamp;

    public MessageDTO $message;

    public ?string $user_locale;
}