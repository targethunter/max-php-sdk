<?php

namespace TH\MAX\Client\DTO\Messages\Attachments;

use TH\MAX\Client\DTO\Messages\Attachments\Payload\SharePayload;
use TH\MAX\DTO\BaseDTO;

class ShareAttachment extends BaseDTO
{
    public string $type = 'share';

    public SharePayload $payload;
}