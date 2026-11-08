<?php

declare(strict_types=1);

namespace TH\MAX\Errors;

/**
 * Понятный текст ошибки MAX на русском: что случилось и что сделать.
 *
 * Тексты не привязаны к конкретному продукту. Если нужен совет про свой интерфейс,
 * подставьте свой текст по MAXError::getReason().
 */
final class MAXErrorTranslator
{
    /**
     * Причина → текст. Если вариантов несколько, берётся первый, для которого есть
     * все параметры из фигурных скобок.
     */
    private const MESSAGES_RU = [
        MAXErrorReason::TOKEN_INVALID => 'Токен бота недействителен или отозван. Проверьте токен бота.',
        MAXErrorReason::BOT_BLOCKED => 'Аккаунт бота в MAX заблокирован. Проверьте состояние бота в MAX.',
        MAXErrorReason::DIALOG_SUSPENDED =>
            'Пользователь остановил диалог с ботом, поэтому сообщения ему не доставляются. Исправлять ничего не нужно.',
        MAXErrorReason::DIALOG_NOT_FOUND =>
            'Диалог с пользователем не найден: пользователь ещё не писал боту или удалил переписку.',
        MAXErrorReason::CHAT_NOT_FOUND => 'Чат не найден: его удалили или бота в нём нет.',
        MAXErrorReason::NOT_FOUND => 'MAX не нашёл указанный чат, пользователя или сообщение.',
        MAXErrorReason::BOT_TO_BOT => 'MAX не разрешает ботам отправлять сообщения другим ботам.',
        MAXErrorReason::RATE_LIMITED => [
            'MAX ограничил частоту запросов: не больше {limit} запросов в секунду. Повторите попытку чуть позже.',
            'MAX ограничил частоту запросов. Повторите попытку чуть позже.',
        ],
        MAXErrorReason::TEXT_EMPTY => 'MAX не принимает сообщение без текста. Добавьте текст сообщения.',
        MAXErrorReason::FIELD_REQUIRED => 'Не заполнено обязательное поле «{field}». Заполните его.',
        MAXErrorReason::TEXT_TOO_LONG => 'Текст сообщения длиннее {limit} символов (сейчас {size}). Сократите текст.',
        MAXErrorReason::FIELD_TOO_LONG => [
            'Значение поля «{field}» длиннее допустимого: {size} при пределе {limit}. Сократите его.',
            'Одно из значений в запросе длиннее допустимого. Сократите его.',
        ],
        MAXErrorReason::BUTTON_LINK_INVALID =>
            'В кнопках-ссылках допустимы только адреса, начинающиеся с http:// или https://. Исправьте ссылку в кнопке.',
        MAXErrorReason::ONE_FILE_ONLY =>
            'Файл можно отправить только отдельным сообщением, без других файлов, изображений и видео.',
        MAXErrorReason::FILE_EMPTY => 'Файл пустой. Загрузите файл с содержимым.',
        MAXErrorReason::ATTACHMENT_NOT_READY => 'Вложение ещё обрабатывается в MAX. Повторите отправку чуть позже.',
        MAXErrorReason::ATTACHMENT_INVALID => 'Вложение недоступно: его токен недействителен. Загрузите файл заново.',
        MAXErrorReason::ATTACHMENT_MALFORMED => 'MAX не принял вложение: в запросе нет файла или его токена.',
        MAXErrorReason::CALLBACK_EXPIRED => 'Нажатие кнопки устарело: ответить на него уже нельзя.',
        MAXErrorReason::NOT_FOR_DIALOGS =>
            'Действие доступно только в групповом чате, в личном диалоге его выполнить нельзя.',
        MAXErrorReason::NO_RIGHTS => 'У бота нет прав на это действие в чате. Проверьте права бота в настройках чата.',
        MAXErrorReason::NO_RIGHTS_DELETE_MESSAGE =>
            'У бота нет прав удалить это сообщение: в личном диалоге бот удаляет только свои сообщения, '
            . 'а в групповом чате ему нужны права администратора на удаление.',
        MAXErrorReason::NO_RIGHTS_DELETE_COMMENT =>
            'У бота нет прав удалить комментарий: нужны права администратора канала на удаление.',
        MAXErrorReason::WRITE_NEEDS_READ => 'Нельзя назначить эти права без права «Читать сообщения».',
        // При обрыве связи MAX мог уже выполнить запрос: повтор отправки даст дубль
        MAXErrorReason::TIMEOUT => 'MAX не ответил вовремя: запрос мог не выполниться. Проверьте результат, прежде чем повторять.',
        MAXErrorReason::EMPTY_RESPONSE =>
            'MAX вернул пустой ответ: запрос мог не выполниться. Проверьте результат, прежде чем повторять.',
        MAXErrorReason::SERVER_ERROR => 'На стороне MAX произошла ошибка. Попробуйте позже.',
    ];

    /**
     * @return string|null текст на русском; null, если ошибка не распознана
     */
    public static function translate(MAXError $error): ?string
    {
        $variants = (array)(self::MESSAGES_RU[$error->getReason()] ?? []);
        $params = $error->getParams();

        $replace = [];
        foreach ($params as $name => $value) {
            $replace['{' . $name . '}'] = (string)$value;
        }

        foreach ($variants as $text) {
            preg_match_all('/\{(\w+)\}/', $text, $matches);
            if (array_diff($matches[1], array_keys($params)) === []) {
                return strtr($text, $replace);
            }
        }

        return null;
    }
}
