<?php

namespace TH\MAX\Client\Modules\Bots;

use TH\MAX\Client\DTO\Bots\MeDTO;
use TH\MAX\Client\DTO\Bots\UpdateDTO;
use TH\MAX\Client\Modules\CommonModule;
use TH\MAX\Interfaces\MAXRequestInterface;

class Bots extends CommonModule
{
    public function __construct(MAXRequestInterface $request)
    {
        parent::__construct($request);
    }

    public function getMe(): MeDTO
    {
        return new MeDTO((array)$this->get('/me'));
    }

    public function update(
        ?string $first_name,
        ?string $last_name,
        ?string $name,
        ?string $description,
        ?array $commands,
        ?string $photo
    ): UpdateDTO {
        return new UpdateDTO(
            (array)$this->patch('/me', [
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