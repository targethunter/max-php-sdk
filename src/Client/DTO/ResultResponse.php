<?php

namespace TH\MAX\Client\DTO;

use TH\MAX\DTO\BaseDTO;

class ResultResponse extends BaseDTO
{
    public bool $success;

    public ?string $message;
}