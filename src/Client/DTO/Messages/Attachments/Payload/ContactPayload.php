<?php

namespace TH\MAX\Client\DTO\Messages\Attachments\Payload;

use TH\MAX\DTO\BaseDTO;

class ContactPayload extends BaseDTO
{
    /**
     * Имя контакта
     */
    public ?string $name = null;

    /**
     * ID контакта, если он зарегистрирован в MAX
     */
    public ?int $contact_id = null;

    /**
     * Полная информация о контакте в формате VCF
     */
    public ?string $vcf_info = null;

    /**
     * Телефон контакта в формате VCF
     */
    public ?string $vcf_phone = null;
}