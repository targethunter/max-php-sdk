<?php

declare(strict_types=1);

namespace TH\MAX\Client\Request;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\ResponseInterface;
use TH\MAX\Config\MAXConfig;
use TH\MAX\Interfaces\MAXRequestInterface;

class MAXRequest implements MAXRequestInterface
{
    private ClientInterface $client;

    public function __construct(
        ?ClientInterface $client = null
    ) {
        $this->client = $client ?? new Client();
    }

    /**
     * @throws GuzzleException
     */
    public function get(string $method, array $params = [], array $headers = []): ResponseInterface
    {
        return $this->client->get($this->getURL($method), [
            'query' => $params,
            'headers' => $headers,
        ]);
    }

    /**
     * @throws GuzzleException
     */
    public function post(string $method, array $params = [], array $headers = []): ResponseInterface
    {
        return $this->client->post($this->getURL($method), [
            'form_params' => $params,
            'headers' => $headers,
        ]);
    }

    /**
     * @throws GuzzleException
     */
    public function postJSON(string $method, array $params = [], array $headers = []): ResponseInterface
    {
        return $this->client->post($this->getURL($method), [
            'json' => $params,
            'headers' => $headers,
        ]);
    }

    /**
     * @throws GuzzleException
     */
    public function putJSON(string $method, array $params = [], array $headers = []): ResponseInterface
    {
        return $this->client->put($this->getURL($method), [
            'json' => $params,
            'headers' => $headers,
        ]);
    }

    /**
     * @throws GuzzleException
     */
    public function patchJSON(string $method, array $params = [], array $headers = []): ResponseInterface
    {
        return $this->client->patch($this->getURL($method), [
            'json' => $params,
            'headers' => $headers,
        ]);
    }

    /**
     * @throws GuzzleException
     */
    public function deleteJSON(string $method, array $params = [], array $headers = []): ResponseInterface
    {
        return $this->client->delete($this->getURL($method), [
            'json' => $params,
            'headers' => $headers,
        ]);
    }

    private function getURL(string $method): string
    {
        return MAXConfig::API_URL . ltrim($method, '/');
    }
}