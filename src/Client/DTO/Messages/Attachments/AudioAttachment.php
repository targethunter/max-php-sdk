<?php

namespace TH\MAX\Client\DTO\Messages\Attachments;

use TH\MAX\Client\DTO\Messages\Attachments\Payload\AudioPayload;
use TH\MAX\DTO\BaseDTO;

class AudioAttachment extends BaseDTO
{
    public string $type = 'audio';

    public AudioPayload $payload;
}