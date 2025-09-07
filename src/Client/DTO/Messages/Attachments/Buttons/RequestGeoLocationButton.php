<?php

namespace TH\MAX\Client\DTO\Messages\Attachments\Buttons;

use TH\MAX\DTO\BaseDTO;

class RequestGeoLocationButton extends BaseDTO
{
    public string $type = 'request_geo_location';

    /**
     * От 1 до 128 символов
     *
     * Видимый текст кнопки
     */
    public string $text;

    /**
     * Если true, отправляет местоположение без запроса подтверждения пользователя
     */
    public bool $quick = false;
}