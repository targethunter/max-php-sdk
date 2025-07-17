<?php

declare(strict_types=1);

namespace TH\MAX\Client;

use TH\MAX\Interfaces\MAXRequestInterface;

class MAXClient
{
    private MAXRequestInterface $request;

    public function __construct(MAXRequestInterface $request)
    {
        $this->request = $request;
    }
}