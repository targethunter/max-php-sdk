<?php

namespace TH\MAX\Client\Modules\Bots;

use TH\MAX\Client\DTO\Bots\Bot;
use TH\MAX\Client\Modules\CommonModule;
use TH\MAX\Interfaces\MAXRequestInterface;

class Bots extends CommonModule
{
    public function __construct(MAXRequestInterface $request)
    {
        parent::__construct($request);
    }

    public function getMe(): Bot
    {
        return new Bot($this->get('/me'));
    }

    public function update(
        ?string $first_name = null,
        ?string $last_name = null,
        ?string $name = null,
        ?string $description = null,
        ?array $commands = null,
        ?string $photo = null
    ): Bot {
        return new Bot($this->patch('/me', [
            'first_name' => $first_name,
            'last_name' => $last_name,
            'name' => $name,
            'description' => $description,
            'commands' => $commands,
            'photo' => $photo
        ])
        );
    }
}