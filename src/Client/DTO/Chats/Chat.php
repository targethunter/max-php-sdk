<?php

namespace TH\MAX\Client\DTO\Chats;

use TH\MAX\Client\DTO\Messages\Message;
use TH\MAX\DTO\BaseDTO;

class Chat extends BaseDTO
{
    public int $chat_id;

    public string $type;

    public string $status;

    public ?string $title;

    public ?Image $icon;

    public int $last_event_time;

    public int $participants_count;

    public ?string $owner_id;

    public ?array $participants;

    public bool $is_public;

    public ?string $link;

    public ?string $description;

    public ?UserWithPhoto $dialog_with_user;

    public ?int $messages_count;

    public ?string $chat_message_id;

    public ?Message $pinned_message;
}