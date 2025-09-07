<?php

namespace TH\MAX\Client\DTO\Upload;

use TH\MAX\DTO\BaseDTO;

class Url extends BaseDTO
{
    public string $url;

    public ?string $token;
}