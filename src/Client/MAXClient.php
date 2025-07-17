<?php

declare(strict_types=1);

namespace TH\MAX\Client;

use TH\MAX\Client\Modules\Bots\Bots;
use TH\MAX\Interfaces\MAXRequestInterface;

class MAXClient
{
    private MAXRequestInterface $request;

    public function __construct(MAXRequestInterface $request)
    {
        $this->request = $request;
    }

    public function bots(): Bots
    {
        return new Bots($this->request);
    }
}