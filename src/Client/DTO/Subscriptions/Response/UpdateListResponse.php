<?php

namespace TH\MAX\Client\DTO\Subscriptions\Response;

use TH\MAX\Client\DTO\Subscriptions\Collection\UpdateCollection;
use TH\MAX\DTO\BaseDTO;

class UpdateListResponse extends BaseDTO
{
    public UpdateCollection $items;

    public ?int $marker;
}