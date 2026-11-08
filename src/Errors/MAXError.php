<?php

declare(strict_types=1);

namespace TH\MAX\Errors;

use GuzzleHttp\Exception\GuzzleException;

/**
 * Ошибка MAX API в разобранном виде: HTTP-код, код и текст из ответа MAX,
 * а также распознанная причина (MAXErrorReason) с параметрами.
 *
 * MAX отвечает на ошибку так: {"code": "verify.token", "message": "Invalid access_token"}.
 */
final class MAXError
{
    private int $status;
    private ?string $error_code;
    private string $message;
    private string $reason;
    /** @var array<string, int|string> */
    private array $params;

    /**
     * @param int $status HTTP-код ответа; 0 — ответа не было (сбой сети)
     * @param string|null $error_code поле code ответа MAX
     * @param string $message текст ошибки
     */
    public function __construct(int $status, ?string $error_code, string $message)
    {
        $this->status = $status;
        $this->error_code = $error_code;
        $this->message = $message;
        [$this->reason, $this->params] = MAXErrorRules::recognize($status, $error_code, $message);
    }

    /**
     * Разбирает любое исключение: ищет в цепочке getPrevious() исключение Guzzle с ответом MAX.
     * Подходит и для своих классов исключений из MAXRequest::createException().
     */
    public static function fromThrowable(\Throwable $exception): self
    {
        for ($current = $exception; $current !== null; $current = $current->getPrevious()) {
            if ($current instanceof GuzzleException) {
                return self::fromGuzzleException($current);
            }
        }

        $code = $exception->getCode();

        return new self(is_int($code) ? $code : 0, null, $exception->getMessage());
    }

    public static function fromGuzzleException(GuzzleException $exception): self
    {
        $response = method_exists($exception, 'getResponse') ? $exception->getResponse() : null;
        $code = $exception->getCode();
        $status = $response ? $response->getStatusCode() : (is_int($code) ? $code : 0);
        $message = $exception->getMessage();
        $error_code = null;

        if ($response) {
            $raw = (string)$response->getBody();
            $data = json_decode($raw, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($data)) {
                $candidates = [
                    $data['message'] ?? null,
                    $data['error']['message'] ?? null,
                    $data['error']['description'] ?? null,
                    $data['description'] ?? null,
                ];
                foreach ($candidates as $candidate) {
                    if (is_string($candidate)) {
                        $message = $candidate;
                        break;
                    }
                }
                $error_code = isset($data['code']) && is_string($data['code']) ? $data['code'] : null;
            }
            elseif ($raw) {
                $message = $raw;
            }
        }

        return new self($status, $error_code, $message);
    }

    /**
     * Для уже сохранённого текста ошибки, когда ответа MAX под рукой нет.
     */
    public static function fromMessage(string $message, int $status = 0, ?string $error_code = null): self
    {
        return new self($status, $error_code, $message);
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function getErrorCode(): ?string
    {
        return $this->error_code;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @see MAXErrorReason
     */
    public function getReason(): string
    {
        return $this->reason;
    }

    /**
     * Параметры из текста ошибки: limit, size, field, chat_id.
     *
     * @return array<string, int|string>
     */
    public function getParams(): array
    {
        return $this->params;
    }

    public function isRecognized(): bool
    {
        return $this->reason !== MAXErrorReason::UNKNOWN;
    }
}
