<?php

namespace TH\MAX\Client\DTO\Messages\Response;

use TH\MAX\Client\DTO\Messages\Message;
use TH\MAX\DTO\BaseDTO;

class MessageResponse extends BaseDTO
{
    public Message $message;
}