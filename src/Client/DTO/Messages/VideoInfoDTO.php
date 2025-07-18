<?php

namespace TH\MAX\Client\DTO\Messages;

use TH\MAX\DTO\BaseDTO;

class VideoInfoDTO extends BaseDTO
{
    public string $token;

    public array $urls;

    public array $thumbnail;

    public int $width;

    public int $height;

    public int $duration;
}