<?php

declare(strict_types=1);

namespace TH\MAX\Errors;

/**
 * Правила распознавания ошибок MAX API.
 *
 * Правила проверяются по порядку, первое совпавшее определяет причину.
 * В правиле задаётся одно или несколько условий, совпасть должны все:
 * - status — HTTP-код ответа;
 * - code — поле code ответа MAX (например, verify.token);
 * - pattern — регулярное выражение по тексту ошибки. Именованные группы
 *   становятся параметрами ошибки: числа приводятся к int.
 *
 * Тексты в шаблонах взяты из настоящих ответов MAX.
 */
final class MAXErrorRules
{
    private const RULES = [
        ['reason' => MAXErrorReason::TOKEN_INVALID, 'code' => 'verify.token'],
        ['reason' => MAXErrorReason::TOKEN_INVALID, 'pattern' => '/^Invalid access_token$/'],
        ['reason' => MAXErrorReason::BOT_BLOCKED, 'pattern' => '/^Account was blocked/'],
        ['reason' => MAXErrorReason::DIALOG_SUSPENDED, 'pattern' => '/\berror\.dialog\.suspended\b/'],
        ['reason' => MAXErrorReason::DIALOG_NOT_FOUND, 'pattern' => '/\berror\.dialog\.notfound\b/'],
        ['reason' => MAXErrorReason::CHAT_NOT_FOUND, 'pattern' => '/^Chat (?<chat_id>-?\d+) not found$/'],
        ['reason' => MAXErrorReason::NOT_FOUND, 'pattern' => '/^error\.not\.found$/'],
        ['reason' => MAXErrorReason::BOT_TO_BOT, 'pattern' => '/\berrors\.send-message\.bot-to-bot\.restriction\b/'],
        [
            'reason' => MAXErrorReason::RATE_LIMITED,
            'pattern' => '/^Too many requests\. Limit is: (?<limit>\d+) calls per second/',
        ],
        // Без имени поля MAX так отвечает на сообщение без текста (например, только с кнопками)
        ['reason' => MAXErrorReason::TEXT_EMPTY, 'pattern' => '/^errors\.required$/'],
        ['reason' => MAXErrorReason::FIELD_REQUIRED, 'pattern' => '/^(?<field>\S+): errors\.required$/'],
        ['reason' => MAXErrorReason::TEXT_EMPTY, 'pattern' => "/Field 'text' size \\(0\\) must be at least 1/"],
        [
            'reason' => MAXErrorReason::TEXT_TOO_LONG,
            'pattern' => "/Field 'text' size \\((?<size>\\d+)\\) must be at most (?<limit>\\d+)/",
        ],
        [
            'reason' => MAXErrorReason::FIELD_TOO_LONG,
            'pattern' => "/Field '(?<field>[^']+)' size \\((?<size>\\d+)\\) must be at most (?<limit>\\d+)/",
        ],
        ['reason' => MAXErrorReason::FIELD_TOO_LONG, 'pattern' => '/\bsize\b.*must be at most/'],
        ['reason' => MAXErrorReason::BUTTON_LINK_INVALID, 'pattern' => '/Must have only http\/https links format in buttons/'],
        ['reason' => MAXErrorReason::ONE_FILE_ONLY, 'pattern' => '/Must be only one file attachment in message/'],
        ['reason' => MAXErrorReason::FILE_EMPTY, 'pattern' => '/Zero file content in direct mode/'],
        ['reason' => MAXErrorReason::ATTACHMENT_NOT_READY, 'pattern' => '/\battachment\.not\.ready\b/'],
        [
            'reason' => MAXErrorReason::ATTACHMENT_NOT_READY,
            'pattern' => '/\berrors\.process\.attachment\.file\.not\.processed\b/',
        ],
        // Токен загруженного файла устарел или неверен
        ['reason' => MAXErrorReason::ATTACHMENT_INVALID, 'pattern' => '/^Invalid `token`|Invalid photo token provided/'],
        // Во вложении нет файла или его токена — ошибка сборки запроса
        [
            'reason' => MAXErrorReason::ATTACHMENT_MALFORMED,
            'pattern' => '/Missing `token` or `fileId`|No `photos`, `url` or `token` provided/',
        ],
        ['reason' => MAXErrorReason::CALLBACK_INVALID, 'pattern' => '/Missing required parameter: callback_id/'],
        ['reason' => MAXErrorReason::NOT_FOR_DIALOGS, 'pattern' => '/Method is not available for dialogs/'],
        ['reason' => MAXErrorReason::NO_RIGHTS_DELETE_MESSAGE, 'pattern' => '/Insufficient permissions to delete message/'],
        ['reason' => MAXErrorReason::NO_RIGHTS_DELETE_COMMENT, 'pattern' => '/Insufficient permissions to delete comment/'],
        ['reason' => MAXErrorReason::NO_RIGHTS, 'pattern' => '/Insufficient access rights to perform this action/'],
        ['reason' => MAXErrorReason::WRITE_NEEDS_READ, 'pattern' => '/message modify permission/'],
        ['reason' => MAXErrorReason::TIMEOUT, 'pattern' => '/^cURL error 28\b/'],
        ['reason' => MAXErrorReason::EMPTY_RESPONSE, 'pattern' => '/^cURL error 52\b/'],
        // Общие правила по HTTP-коду — последними: конкретный текст точнее кода
        ['reason' => MAXErrorReason::TOKEN_INVALID, 'status' => 401],
        ['reason' => MAXErrorReason::RATE_LIMITED, 'status' => 429],
        ['reason' => MAXErrorReason::SERVER_ERROR, 'status' => 500],
        ['reason' => MAXErrorReason::SERVER_ERROR, 'status' => 502],
        ['reason' => MAXErrorReason::SERVER_ERROR, 'status' => 503],
        // Шлюз MAX не дождался ответа — тот же таймаут: запрос мог выполниться
        ['reason' => MAXErrorReason::TIMEOUT, 'status' => 504],
    ];

    /**
     * @return array{0: string, 1: array<string, int|string>} причина и параметры
     */
    public static function recognize(int $status, ?string $code, string $message): array
    {
        foreach (self::RULES as $rule) {
            if (isset($rule['status']) && $rule['status'] !== $status) {
                continue;
            }
            if (isset($rule['code']) && $rule['code'] !== $code) {
                continue;
            }
            $matches = [];
            if (isset($rule['pattern']) && !preg_match($rule['pattern'], $message, $matches, PREG_UNMATCHED_AS_NULL)) {
                continue;
            }

            return [$rule['reason'], self::params($matches)];
        }

        return [MAXErrorReason::UNKNOWN, []];
    }

    /**
     * @return array<string, int|string>
     */
    private static function params(array $matches): array
    {
        $params = [];
        foreach ($matches as $name => $value) {
            // Не совпавшая необязательная группа приходит null — такого параметра нет
            if (is_string($name) && $value !== null) {
                $params[$name] = preg_match('/^-?\d+$/', $value) ? (int)$value : $value;
            }
        }

        return $params;
    }
}
