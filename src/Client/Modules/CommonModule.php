<?php

namespace TH\MAX\Client\Modules;

use Psr\Http\Message\ResponseInterface;
use TH\MAX\Interfaces\MAXRequestInterface;

abstract class CommonModule
{
    protected MAXRequestInterface $request;

    public function __construct(MAXRequestInterface $request)
    {
        $this->request = $request;
    }

    protected function get(string $method, ?array $params = null): ResponseInterface
    {
        return $this->request->get($method, $params ?? []);
    }

    protected function patch(string $method, ?array $params = null): ResponseInterface
    {
        return $this->request->patchJSON($method, $params ?? []);
    }
}