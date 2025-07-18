<?php

namespace TH\MAX\Client\DTO\Chats;

use TH\MAX\DTO\BaseDTO;

class RecipientDTO extends BaseDTO
{
    public int $chat_id;

    public string $chat_type;

    public ?int $user_id;
}