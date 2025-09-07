<?php

namespace TH\MAX\Client\DTO\Messages\Attachments;

use TH\MAX\DTO\BaseDTO;

class LocationAttachment extends BaseDTO
{
    public string $type = 'location';

    public float $latitude;

    public float $longitude;
}