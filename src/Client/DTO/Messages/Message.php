<?php

namespace TH\MAX\Client\DTO\Messages;

use TH\MAX\Client\DTO\Chats\Recipient;
use TH\MAX\Client\DTO\Chats\User;
use TH\MAX\DTO\BaseDTO;

class Message extends BaseDTO
{
    public User $sender;

    public Recipient $recipient;

    public int $timestamp;

    public ?LinkedMessage $link;

    public MessageBody $body;

    public ?MessageStat $stat;

    public ?string $url;
}