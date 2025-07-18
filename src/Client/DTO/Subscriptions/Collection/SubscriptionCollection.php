<?php

namespace TH\MAX\Client\DTO\Subscriptions\Collection;

use TH\MAX\Client\DTO\Chats\ChatDTO;
use TH\MAX\Client\DTO\Subscriptions\SubscriptionDTO;
use TH\MAX\DTO\BaseDTOCollection;

class SubscriptionCollection extends BaseDTOCollection
{
    const ITEM_CLASS = SubscriptionDTO::class;
}