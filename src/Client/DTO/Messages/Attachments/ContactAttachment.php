<?php

namespace TH\MAX\Client\DTO\Messages\Attachments;

use TH\MAX\Client\DTO\Messages\Attachments\Payload\ContactPayload;
use TH\MAX\DTO\BaseDTO;

class ContactAttachment extends BaseDTO
{
    public string $type = 'contact';

    public ContactPayload $payload;
}