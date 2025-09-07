<?php

namespace TH\MAX\Client\DTO\Chats\Collection;

use TH\MAX\Client\DTO\Chats\ChatMember;
use TH\MAX\DTO\BaseDTOCollection;

class ChatMemberCollection extends BaseDTOCollection
{
    const ITEM_CLASS = ChatMember::class;
}