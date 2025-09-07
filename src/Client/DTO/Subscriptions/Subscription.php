<?php

namespace TH\MAX\Client\DTO\Subscriptions;

use TH\MAX\DTO\BaseDTO;

class Subscription extends BaseDTO
{
    public string $url;

    public int $time;

    public ?array $update_types;
}