<?php

namespace TH\MAX\Client\DTO\Messages;

use TH\MAX\Client\DTO\Chats\RecipientDTO;
use TH\MAX\Client\DTO\Chats\UserDTO;
use TH\MAX\DTO\BaseDTO;

class MessageDTO extends BaseDTO
{
    public UserDTO $sender;

    public RecipientDTO $recipient;

    public int $timestamp;

    public ?LinkedMessageDTO $link;

    public MessageBodyDTO $body;

    public ?MessageStatDTO $stat;

    public ?string $url;
}