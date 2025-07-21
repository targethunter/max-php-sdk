<?php

namespace TH\MAX\Client\DTO\Messages\Response;

use TH\MAX\Client\DTO\Messages\MessageDTO;
use TH\MAX\DTO\BaseDTO;

class MessageResponse extends BaseDTO
{
    public MessageDTO $message;
}