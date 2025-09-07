<?php

namespace TH\MAX\Client\DTO\Chats;

use TH\MAX\DTO\BaseDTO;

class ChatAdmins extends BaseDTO
{
    public int $user_id;

    public array $permissions;

    public string $alias;
}