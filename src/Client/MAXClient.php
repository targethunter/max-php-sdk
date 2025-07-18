<?php

declare(strict_types=1);

namespace TH\MAX\Client;

use TH\MAX\Client\Modules\Bots\Bots;
use TH\MAX\Client\Modules\Chats\Chats;
use TH\MAX\Client\Modules\Messages\Messages;
use TH\MAX\Client\Modules\Subscriptions\Subscriptions;
use TH\MAX\Client\Modules\Upload\Upload;
use TH\MAX\Interfaces\MAXRequestInterface;

class MAXClient
{
    private MAXRequestInterface $request;

    public function __construct(MAXRequestInterface $request)
    {
        $this->request = $request;
    }

    public function bots(): Bots
    {
        return new Bots($this->request);
    }

    public function chats(): Chats
    {
        return new Chats($this->request);
    }

    public function subscriptions(): Subscriptions
    {
        return new Subscriptions($this->request);
    }

    public function upload(): Upload
    {
        return new Upload($this->request);
    }

    public function messages(): Messages
    {
        return new Messages($this->request);
    }
}