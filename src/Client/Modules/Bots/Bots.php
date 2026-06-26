<?php

namespace TH\MAX\Client\Modules\Bots;

use TH\MAX\Client\DTO\Bots\Bot;
use TH\MAX\Client\Modules\CommonModule;
use TH\MAX\Interfaces\MAXRequestInterface;

class Bots extends CommonModule
{
    public function __construct(MAXRequestInterface $request)
    {
        parent::__construct($request);
    }

    /**
     * @see https://dev.max.ru/docs-api/methods/GET/me
     */
    public function getMe(): Bot
    {
        return new Bot($this->getRequest('/me'));
    }
}
