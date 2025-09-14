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

    protected function getRequest(string $method, ?array $params = null): array
    {
        $response = $this->request->get($method, $params ?? []);
        return $this->getResponse($response);
    }

    protected function postRequest(string $method, ?array $params = null, ?array $query_params = null): array
    {
        $response = $this->request->postJSON($method, $params ?? [], $query_params ?? []);
        return $this->getResponse($response);
    }

    protected function putRequest(string $method, ?array $params = null, ?array $query_params = null): array
    {
        $response = $this->request->putJSON($method, $params ?? [], $query_params ?? []);
        return $this->getResponse($response);
    }

    protected function patchRequest(string $method, ?array $params = null): array
    {
        $response = $this->request->patchJSON($method, $params ?? []);
        return $this->getResponse($response);
    }

    protected function deleteRequest(string $method, ?array $params = null): array
    {
        $response = $this->request->deleteJSON($method, $params ?? []);
        return $this->getResponse($response);
    }

    private function getResponse(ResponseInterface $response): array
    {
        return json_decode($response->getBody()->getContents(), true);
    }
}