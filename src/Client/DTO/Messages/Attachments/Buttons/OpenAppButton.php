<?php

namespace TH\MAX\Client\DTO\Messages\Attachments\Buttons;

use TH\MAX\DTO\BaseDTO;

class OpenAppButton extends BaseDTO
{
    public string $type = 'open_app';

    /**
     * От 1 до 128 символов
     *
     * Видимый текст кнопки
     */
    public string $text;

    /**
     * Публичное имя (username) бота или ссылка на него, чьё мини-приложение надо запустить
     */
    public ?string $web_app = null;

    /**
     * Идентификатор бота, чьё мини-приложение надо запустить
     */
    public ?int $contact_id = null;

    /**
     * Параметр запуска, который будет передан в initData мини-приложения
     */
    public ?string $payload = null;
}