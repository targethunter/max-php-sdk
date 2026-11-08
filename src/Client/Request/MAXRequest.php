<?php

declare(strict_types=1);

namespace TH\MAX\Client\Request;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\ResponseInterface;
use TH\MAX\Config\MAXConfig;
use TH\MAX\Errors\MAXError;
use TH\MAX\Errors\MAXErrorTranslator;
use TH\MAX\Exceptions\MAXHttpException;
use TH\MAX\Interfaces\MAXRequestInterface;

class MAXRequest implements MAXRequestInterface
{
    private ClientInterface $client;
    private string $access_token;
    // Значение по умолчанию — для наследников, которые не вызывают родительский конструктор
    private bool $translate_errors = false;

    /**
     * @param bool $translate_errors true — текст исключения на русском (MAXErrorTranslator), если ошибка
     *                               распознана. По умолчанию выключено: текст как в ответе MAX, как раньше.
     */
    public function __construct(
        string $access_token,
        ?ClientInterface $client = null,
        bool $translate_errors = false
    ) {
        $this->client = $client ?? new Client();
        $this->access_token = $access_token;
        $this->translate_errors = $translate_errors;
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
        // Текст ошибки — из полей ответа MAX, а если ответ не JSON — сырое тело
        $error = MAXError::fromGuzzleException($e);
        $message = $error->getMessage();
        if ($this->translate_errors) {
            // Исходный текст MAX остаётся в getMAXError()->getMessage()
            $message = MAXErrorTranslator::translate($error) ?? $message;
        }
        $exception = $this->createException($message, $error->getStatus(), $e);
        if ($exception instanceof MAXHttpException) {
            $exception->setMAXError($error);
        }

        return $exception;
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