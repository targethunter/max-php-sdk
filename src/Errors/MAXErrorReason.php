<?php

declare(strict_types=1);

namespace TH\MAX\Errors;

/**
 * Причины ошибок MAX API. Значения постоянные: на них можно опираться в коде
 * и хранить в базе. Причину определяет MAXErrorRules по ответу MAX.
 */
final class MAXErrorReason
{
    /** Токен бота недействителен или отозван */
    public const TOKEN_INVALID = 'token_invalid';

    /** Аккаунт бота заблокирован в MAX */
    public const BOT_BLOCKED = 'bot_blocked';

    /** Пользователь остановил диалог с ботом */
    public const DIALOG_SUSPENDED = 'dialog_suspended';

    /** Диалога с пользователем нет: он не писал боту или удалил переписку */
    public const DIALOG_NOT_FOUND = 'dialog_not_found';

    /** Чат удалён или бота в нём нет */
    public const CHAT_NOT_FOUND = 'chat_not_found';

    /** MAX не нашёл чат, пользователя или сообщение */
    public const NOT_FOUND = 'not_found';

    /** Бот пытается написать другому боту */
    public const BOT_TO_BOT = 'bot_to_bot';

    /** Слишком много запросов */
    public const RATE_LIMITED = 'rate_limited';

    /** Сообщение без текста */
    public const TEXT_EMPTY = 'text_empty';

    /** Не заполнено обязательное поле сообщения */
    public const FIELD_REQUIRED = 'field_required';

    /** Текст сообщения длиннее предела */
    public const TEXT_TOO_LONG = 'text_too_long';

    /** Другое поле сообщения длиннее предела */
    public const FIELD_TOO_LONG = 'field_too_long';

    /** В кнопке-ссылке адрес не http(s) */
    public const BUTTON_LINK_INVALID = 'button_link_invalid';

    /** Файл вместе с другими вложениями */
    public const ONE_FILE_ONLY = 'one_file_only';

    /** Пустой файл */
    public const FILE_EMPTY = 'file_empty';

    /** Вложение ещё обрабатывается */
    public const ATTACHMENT_NOT_READY = 'attachment_not_ready';

    /** Токен вложения недействителен */
    public const ATTACHMENT_INVALID = 'attachment_invalid';

    /** Ответ на нажатие кнопки опоздал */
    public const CALLBACK_EXPIRED = 'callback_expired';

    /** Действие работает только в групповом чате */
    public const NOT_FOR_DIALOGS = 'not_for_dialogs';

    /** У бота нет прав на действие */
    public const NO_RIGHTS = 'no_rights';

    /** У бота нет прав удалить сообщение */
    public const NO_RIGHTS_DELETE_MESSAGE = 'no_rights_delete_message';

    /** У бота нет прав удалить комментарий */
    public const NO_RIGHTS_DELETE_COMMENT = 'no_rights_delete_comment';

    /** Право на изменение сообщений нельзя выдать без права их читать */
    public const WRITE_NEEDS_READ = 'write_needs_read';

    /** MAX не ответил вовремя */
    public const TIMEOUT = 'timeout';

    /** MAX вернул пустой ответ */
    public const EMPTY_RESPONSE = 'empty_response';

    /** Ошибка на стороне сервера MAX */
    public const SERVER_ERROR = 'server_error';

    /** Ошибка не распознана */
    public const UNKNOWN = 'unknown';

    /**
     * Все причины.
     *
     * @return string[]
     */
    public static function all(): array
    {
        return array_values((new \ReflectionClass(self::class))->getConstants());
    }
}
