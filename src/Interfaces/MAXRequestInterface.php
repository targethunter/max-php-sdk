<?php

declare(strict_types=1);

namespace TH\MAX\Interfaces;

use Psr\Http\Message\ResponseInterface;

interface MAXRequestInterface
{
    public function get(string $method, array $params = [], array $headers = []): ResponseInterface;

    public function post(string $method, array $params = [], array $headers = []): ResponseInterface;

    public function postJSON(string $method, array $params = [], array $query_params = [], array $headers = []): ResponseInterface;

    public function putJSON(string $method, array $params = [], array $headers = []): ResponseInterface;

    public function patchJSON(string $method, array $params = [], array $headers = []): ResponseInterface;

    public function deleteJSON(string $method, array $params = [], array $headers = []): ResponseInterface;
}