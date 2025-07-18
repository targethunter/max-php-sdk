<?php

namespace TH\MAX\Client\DTO\Chats\Collection;

use TH\MAX\Client\DTO\Chats\ChatMemberDTO;
use TH\MAX\DTO\BaseDTOCollection;

class ChatMemberCollection extends BaseDTOCollection
{
    const ITEM_CLASS = ChatMemberDTO::class;
}