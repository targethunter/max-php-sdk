<?php

namespace TH\MAX\Client\DTO\Chats\Response;

use TH\MAX\Client\DTO\Chats\Collection\ChatCollection;
use TH\MAX\DTO\BaseDTO;

class ChatListResponse extends BaseDTO
{
    public ChatCollection $chats;

    public int $marker;
}