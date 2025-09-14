<?php

declare(strict_types=1);

namespace TH\MAX\Exceptions;

use GuzzleHttp\Exception\GuzzleException;
use RuntimeException;

class MAXHttpException extends RuntimeException
{
    private ?GuzzleException $originalException;

    public function __construct(string $message, int $code = 0, ?GuzzleException $originalException = null)
    {
        parent::__construct($message, $code);
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
}
