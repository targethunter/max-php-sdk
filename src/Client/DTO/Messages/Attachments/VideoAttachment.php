<?php

namespace TH\MAX\Client\DTO\Messages\Attachments;

use TH\MAX\Client\DTO\Messages\Attachments\Payload\VideoPayload;
use TH\MAX\DTO\BaseDTO;

class VideoAttachment extends BaseDTO
{
    public string $type = 'video';

    public VideoPayload $payload;
}