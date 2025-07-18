<?php

namespace TH\MAX\Client\DTO\Chats\Response;

use TH\MAX\Client\DTO\Chats\Collection\ChatMemberCollection;
use TH\MAX\DTO\BaseDTO;

class ChatMemberListResponse extends BaseDTO
{
    public ChatMemberCollection $items;

    public ?int $marker;
}