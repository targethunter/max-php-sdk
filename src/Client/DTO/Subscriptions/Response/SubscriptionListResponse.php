<?php

namespace TH\MAX\Client\DTO\Subscriptions\Response;

use TH\MAX\Client\DTO\Subscriptions\Collection\SubscriptionCollection;
use TH\MAX\DTO\BaseDTO;

class SubscriptionListResponse extends BaseDTO
{
    public SubscriptionCollection $subscriptions;
}