<?php

namespace TH\MAX\Client\DTO\Messages\Collection;

use TH\MAX\Client\DTO\Messages\Message;
use TH\MAX\DTO\BaseDTOCollection;

class MessageCollection extends BaseDTOCollection
{
    const ITEM_CLASS = Message::class;
}