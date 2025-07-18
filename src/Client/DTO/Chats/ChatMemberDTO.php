<?php

namespace TH\MAX\Client\DTO\Chats;

use TH\MAX\DTO\BaseDTO;

class ChatMemberDTO extends BaseDTO
{
    public int $user_id;

    public string $first_name;

    public ?string $last_name;

    public ?string $username;

    public bool $is_bot;

    public int $last_activity_time;

    public ?string $description;

    public string $avatar_url;

    public string $full_avatar_url;

    public int $last_access_time;

    public bool $is_owner;

    public bool $is_admin;

    public int $join_time;

    public array $permissions;

    public string $alias;
}