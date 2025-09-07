<?php

namespace TH\MAX\Client\DTO\Messages\Attachments;

use TH\MAX\Client\DTO\Messages\Attachments\Payload\FilePayload;
use TH\MAX\DTO\BaseDTO;

class FileAttachment extends BaseDTO
{
    public string $type = 'file';

    public FilePayload $payload;
}