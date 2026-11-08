<?php

declare(strict_types=1);

namespace TH\MAX\Exceptions;

use GuzzleHttp\Exception\GuzzleException;
use RuntimeException;
use TH\MAX\Errors\MAXError;

class MAXHttpException extends RuntimeException
{
    private ?GuzzleException $originalException;
    private ?MAXError $error = null;

    public function __construct(string $message, int $code = 0, ?GuzzleException $originalException = null)
    {
        parent::__construct($message, $code, $originalException);
        $this->originalException = $originalException;
    }

    public function getOriginalException(): ?GuzzleException
    {
        return $this->originalException;
    }

    public function hasOriginalException(): bool
    {
        return $this->originalException !== null;
    }

    /**
     * Ошибка в разобранном виде: код MAX, причина и параметры.
     */
    public function getError(): MAXError
    {
        if ($this->error === null) {
            $this->error = MAXError::fromThrowable($this);
        }

        return $this->error;
    }
}
