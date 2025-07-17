<?php

namespace TH\MAX\Client\DTO\Bots;

use TH\MAX\DTO\BaseDTO;

class MeDTO extends BaseDTO
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

    public ?array $commands;
}