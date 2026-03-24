<?php

declare(strict_types=1);

namespace TH\MAX\Client\Request;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\ResponseInterface;
use TH\MAX\Config\MAXConfig;
use TH\MAX\Exceptions\MAXHttpException;
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
     * @throws MAXHttpException
     */
    public function get(string $method, array $params = [], array $headers = []): ResponseInterface
    {
        try {
            return $this->client->get($this->getURL($method), [
                'query' => $params,
                'headers' => $this->withAuth($headers),
            ]);
        } catch (GuzzleException $e) {
            throw $this->toMAXHttpException($e);
        }
    }

    /**
     * @throws MAXHttpException
     */
    public function post(string $method, array $params = [], array $headers = []): ResponseInterface
    {
        try {
            return $this->client->post($this->getURL($method), [
                'form_params' => $params,
                'headers' => $this->withAuth($headers),
            ]);
        } catch (GuzzleException $e) {
            throw $this->toMAXHttpException($e);
        }
    }

    /**
     * @throws MAXHttpException
     */
    public function postJSON(string $method, array $params = [], array $query_params = [], array $headers = []): ResponseInterface
    {
        try {
            return $this->client->post($this->getURL($method), [
                'query' => $query_params,
                'json' => $params,
                'headers' => $this->withAuth($headers),
            ]);
        } catch (GuzzleException $e) {
            throw $this->toMAXHttpException($e);
        }
    }

    /**
     * @throws MAXHttpException
     */
    public function putJSON(string $method, array $params = [], array $query_params = [], array $headers = []): ResponseInterface
    {
        try {
            return $this->client->put($this->getURL($method), [
                'query' => $query_params,
                'json' => $params,
                'headers' => $this->withAuth($headers),
            ]);
        } catch (GuzzleException $e) {
            throw $this->toMAXHttpException($e);
        }
    }

    /**
     * @throws MAXHttpException
     */
    public function patchJSON(string $method, array $params = [], array $headers = []): ResponseInterface
    {
        try {
            return $this->client->patch($this->getURL($method), [
                'json' => $params,
                'headers' => $this->withAuth($headers),
            ]);
        } catch (GuzzleException $e) {
            throw $this->toMAXHttpException($e);
        }
    }

    /**
     * @throws MAXHttpException
     */
    public function deleteJSON(string $method, array $params = [], array $headers = []): ResponseInterface
    {
        try {
            return $this->client->delete($this->getURL($method), [
                'query' => $params,
                'headers' => $this->withAuth($headers),
            ]);
        } catch (GuzzleException $e) {
            throw $this->toMAXHttpException($e);
        }
    }

    protected function withAuth(array $headers): array
    {
        $headers['Authorization'] = $this->access_token;
        return $headers;
    }

    protected function getURL(string $method): string
    {
        return MAXConfig::API_URL . ltrim($method, '/');
    }

    protected function toMAXHttpException(GuzzleException $e): \Throwable
    {
        $code = method_exists($e, 'getResponse') && $e->getResponse()
            ? $e->getResponse()->getStatusCode()
            : (is_int($e->getCode()) ? $e->getCode() : 0);

        $msg = $e->getMessage();

        // Если есть тело ответа — попробуем вытащить человекочитаемое сообщение
        if (method_exists($e, 'getResponse') && ($resp = $e->getResponse())) {
            $raw = (string)$resp->getBody();
            $data = json_decode($raw, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($data)) {
                // Популярные поля для MAX API
                $msg = $data['message']
                    ?? $data['error']['message']
                    ?? $data['error']['description']
                    ?? $data['description']
                    ?? $msg;
            }
            elseif ($raw) {
                $msg = $raw; // не JSON — отдадим сырое тело
            }
        }

        return $this->createException($msg, $code, $e);
    }

    /**
     * Фабричный метод для создания исключения.
     * Переопределите для использования собственного класса исключений.
     *
     * @param string $message Человекочитаемое сообщение об ошибке
     * @param int $code HTTP-код ответа
     * @param GuzzleException $original Оригинальное исключение Guzzle
     * @return \Throwable
     */
    protected function createException(string $message, int $code, GuzzleException $original): \Throwable
    {
        return new MAXHttpException($message, $code, $original);
    }
}