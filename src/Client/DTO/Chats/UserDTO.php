<?php

namespace TH\MAX\Client\DTO\Chats;

use TH\MAX\DTO\BaseDTO;

class UserDTO extends BaseDTO
{
    public int $user_id;

    public string $first_name;

    public ?string $last_name;

    public ?string $name;

    public ?string $username;

    public bool $is_bot;

    public int $last_activity_time;
}