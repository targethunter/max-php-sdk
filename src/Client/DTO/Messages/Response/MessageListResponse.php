<?php

namespace TH\MAX\Client\DTO\Messages\Response;

use TH\MAX\Client\DTO\Messages\Collection\MessageCollection;
use TH\MAX\DTO\BaseDTO;

class MessageListResponse extends BaseDTO
{
    public MessageCollection $messages;
}