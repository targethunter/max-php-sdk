<?php

namespace TH\MAX\Client\Modules\Subscriptions;

use TH\MAX\Client\DTO\Bots\BotDTO;
use TH\MAX\Client\DTO\ResultResponse;
use TH\MAX\Client\DTO\Subscriptions\Collection\SubscriptionCollection;
use TH\MAX\Client\DTO\Subscriptions\Collection\UpdateCollection;
use TH\MAX\Client\DTO\Subscriptions\Response\SubscriptionListResponse;
use TH\MAX\Client\DTO\Subscriptions\Response\UpdateListResponse;
use TH\MAX\Client\Modules\CommonModule;
use TH\MAX\Interfaces\MAXRequestInterface;

class Subscriptions extends CommonModule
{
    public function __construct(MAXRequestInterface $request)
    {
        parent::__construct($request);
    }

    public function getAll(): SubscriptionListResponse
    {
        $response = $this->get('/subscriptions');

        $response['items'] = SubscriptionCollection::fromArray($response['subscriptions']);

        return new SubscriptionListResponse($response);
    }

    public function subscribe(
        string $url,
        ?array $update_types = null,
        ?string $secret = null
    ): ResultResponse
    {
        return new ResultResponse(
            $this->post('/subscriptions', [
                'url' => $url,
                'update_types' => $update_types,
                'secret' => $secret,
            ])
        );
    }

    public function unsubscribe(string $url): ResultResponse
    {
        return new ResultResponse(
            $this->delete('/subscriptions', [
                'url' => $url,
            ])
        );
    }

    public function getUpdates(
        int $limit = 100,
        int $timeout = 30,
        ?int $marker = null,
        ?array $types = null
    ): UpdateListResponse
    {
        $response = $this->get('/updates', [
            'limit' => $limit,
            'timeout' => $timeout,
            'marker' => $marker,
            'types' => $types,
        ]);

        $response['items'] = UpdateCollection::fromArray($response['updates']);

        return new UpdateListResponse($response);
    }
}