<?php

namespace TH\MAX\Client\DTO\Messages\Attachments;

use TH\MAX\Client\DTO\Messages\Attachments\Payload\ImagePayload;
use TH\MAX\DTO\BaseDTO;

class ImageAttachment extends BaseDTO
{
    public string $type = 'image';

    public ImagePayload $payload;
}