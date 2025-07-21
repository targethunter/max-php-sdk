<?php

namespace TH\MAX\Client\Modules\Upload;

use TH\MAX\Client\DTO\Upload\UrlDTO;
use TH\MAX\Client\Modules\CommonModule;
use TH\MAX\Interfaces\MAXRequestInterface;

class Upload extends CommonModule
{
    public function __construct(MAXRequestInterface $request)
    {
        parent::__construct($request);
    }

    public function getUrl(string $type): UrlDTO
    {
        return new UrlDTO(
            $this->post('/uploads', [], [
                'type' => $type,
            ])
        );
    }
}