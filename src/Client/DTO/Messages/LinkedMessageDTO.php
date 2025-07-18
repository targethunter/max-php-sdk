<?php

namespace TH\MAX\Client\DTO\Messages;

use TH\MAX\Client\DTO\Chats\UserDTO;
use TH\MAX\DTO\BaseDTO;

class LinkedMessageDTO extends BaseDTO
{
    public string $type;

    public UserDTO $sender;

    public int $chat_id;

    public MessageBodyDTO $message;
}