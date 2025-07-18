<?php

namespace TH\MAX\Client\DTO\Upload;

use TH\MAX\DTO\BaseDTO;

class UrlDTO extends BaseDTO
{
    public string $url;

    public ?string $token;
}