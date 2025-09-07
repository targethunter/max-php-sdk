<?php

namespace TH\MAX\Client\DTO\Messages;

use TH\MAX\Client\DTO\Chats\User;
use TH\MAX\DTO\BaseDTO;

class LinkedMessage extends BaseDTO
{
    public string $type;

    public User $sender;

    public int $chat_id;

    public MessageBody $message;
}