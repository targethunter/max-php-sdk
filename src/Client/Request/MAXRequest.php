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
    private string $access_token;

    public function __construct(
        string $access_token,
        ?ClientInterface $client = null
    ) {
        $this->client = $client ?? new Client();
        $this->access_token = $access_token;
    }

    /**
     * @throws GuzzleException
     */
    public function get(string $method, array $params = [], array $headers = []): ResponseInterface
    {
        return $this->client->get($this->getURL($method), [
            'query' => array_merge($params, ['access_token' => $this->access_token]),
            'headers' => $headers,
        ]);
    }

    /**
     * @throws GuzzleException
     */
    public function post(string $method, array $params = [], array $headers = []): ResponseInterface
    {
        return $this->client->post($this->getURL($method), [
            'query' => ['access_token' => $this->access_token],
            'form_params' => $params,
            'headers' => $headers,
        ]);
    }

    /**
     * @throws GuzzleException
     */
    public function postJSON(string $method, array $params = [], array $query_params = [], array $headers = []): ResponseInterface
    {
        return $this->client->post($this->getURL($method), [
            'query' => array_merge($query_params, ['access_token' => $this->access_token]),
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
            'query' => ['access_token' => $this->access_token],
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
            'query' => ['access_token' => $this->access_token],
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
            'query' => ['access_token' => $this->access_token],
            'json' => $params,
            'headers' => $headers,
        ]);
    }

    private function getURL(string $method): string
    {
        return MAXConfig::API_URL . ltrim($method, '/');
    }
}