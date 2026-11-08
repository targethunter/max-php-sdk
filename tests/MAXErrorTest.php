<?php

declare(strict_types=1);

namespace TH\MAX\Tests;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\NoSeekStream;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Utils;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use TH\MAX\Client\Request\MAXRequest;
use TH\MAX\Errors\MAXError;
use TH\MAX\Errors\MAXErrorReason;
use TH\MAX\Errors\MAXErrorTranslator;
use TH\MAX\Exceptions\MAXHttpException;

class MAXErrorTest extends TestCase
{
    /**
     * Настоящие ответы MAX: HTTP-код, поле code, текст → причина и параметры.
     * На каждую причину, кроме UNKNOWN, здесь есть хотя бы один пример.
     */
    public static function realErrors(): array
    {
        return [
            'неверный токен по коду' => [401, 'verify.token', 'Invalid access_token', MAXErrorReason::TOKEN_INVALID, []],
            'неверный токен по тексту' => [0, null, 'Invalid access_token', MAXErrorReason::TOKEN_INVALID, []],
            'неверный токен по HTTP-коду' => [401, null, 'Unauthorized', MAXErrorReason::TOKEN_INVALID, []],
            'бот заблокирован' => [0, null, 'Account was blocked', MAXErrorReason::BOT_BLOCKED, []],
            'диалог остановлен, обёртка Key' => [
                0, null, 'Key: error.dialog.suspended, args: [123456,].', MAXErrorReason::DIALOG_SUSPENDED, [],
            ],
            'диалог остановлен' => [0, null, 'error.dialog.suspended', MAXErrorReason::DIALOG_SUSPENDED, []],
            'диалога нет' => [0, null, 'error.dialog.notfound', MAXErrorReason::DIALOG_NOT_FOUND, []],
            'чат не найден' => [0, null, 'Chat 123456 not found', MAXErrorReason::CHAT_NOT_FOUND, ['chat_id' => 123456]],
            'групповой чат не найден' => [
                0, null, 'Chat -70123456789 not found', MAXErrorReason::CHAT_NOT_FOUND, ['chat_id' => -70123456789],
            ],
            'не найдено' => [0, null, 'error.not.found', MAXErrorReason::NOT_FOUND, []],
            'бот ботам' => [
                0, null, 'errors.send-message.bot-to-bot.restriction', MAXErrorReason::BOT_TO_BOT, [],
            ],
            'предел запросов' => [
                429, null, 'Too many requests. Limit is: 30 calls per second.', MAXErrorReason::RATE_LIMITED, ['limit' => 30],
            ],
            'предел запросов по коду' => [429, null, 'Too Many Requests', MAXErrorReason::RATE_LIMITED, []],
            'пустой текст, старый вид' => [0, null, 'errors.required', MAXErrorReason::TEXT_EMPTY, []],
            'не заполнено поле' => [
                0, null, 'attachments.0.payload: errors.required', MAXErrorReason::FIELD_REQUIRED,
                ['field' => 'attachments.0.payload'],
            ],
            'пустой текст, новый вид' => [
                0, null, "Field 'text' size (0) must be at least 1", MAXErrorReason::TEXT_EMPTY, [],
            ],
            'длинный текст' => [
                0, null, "Field 'text' size (4003) must be at most 4000", MAXErrorReason::TEXT_TOO_LONG,
                ['size' => 4003, 'limit' => 4000],
            ],
            'длинное поле' => [
                0, null, "Field 'caption' size (1100) must be at most 1024", MAXErrorReason::FIELD_TOO_LONG,
                ['field' => 'caption', 'size' => 1100, 'limit' => 1024],
            ],
            'длинное поле, другой вид' => [0, null, 'size must be at most 128', MAXErrorReason::FIELD_TOO_LONG, []],
            'ссылка в кнопке' => [
                0, null, 'Must have only http/https links format in buttons', MAXErrorReason::BUTTON_LINK_INVALID, [],
            ],
            'один файл' => [0, null, 'Must be only one file attachment in message', MAXErrorReason::ONE_FILE_ONLY, []],
            'пустой файл' => [0, null, 'Zero file content in direct mode', MAXErrorReason::FILE_EMPTY, []],
            'вложение не готово' => [0, null, 'attachment.not.ready', MAXErrorReason::ATTACHMENT_NOT_READY, []],
            'файл не обработан' => [
                0, null, 'errors.process.attachment.file.not.processed', MAXErrorReason::ATTACHMENT_NOT_READY, [],
            ],
            'токен вложения' => [0, null, 'Invalid `token`: f1LHodD2', MAXErrorReason::ATTACHMENT_INVALID, []],
            'токен фото' => [0, null, 'Invalid photo token provided', MAXErrorReason::ATTACHMENT_INVALID, []],
            'нет токена вложения' => [0, null, 'Missing `token` or `fileId`', MAXErrorReason::ATTACHMENT_MALFORMED, []],
            'нет файла во вложении' => [
                0, null, 'No `photos`, `url` or `token` provided', MAXErrorReason::ATTACHMENT_MALFORMED, [],
            ],
            'нет идентификатора нажатия' => [
                0, null, 'Missing required parameter: callback_id', MAXErrorReason::CALLBACK_INVALID, [],
            ],
            'не для диалогов' => [0, null, 'Method is not available for dialogs', MAXErrorReason::NOT_FOR_DIALOGS, []],
            'нет прав' => [
                0, null, 'Insufficient access rights to perform this action', MAXErrorReason::NO_RIGHTS, [],
            ],
            'нет прав удалить сообщение' => [
                0, null, 'Insufficient permissions to delete message', MAXErrorReason::NO_RIGHTS_DELETE_MESSAGE, [],
            ],
            'нет прав удалить комментарий' => [
                0, null, 'Insufficient permissions to delete comment', MAXErrorReason::NO_RIGHTS_DELETE_COMMENT, [],
            ],
            'изменение без чтения' => [
                0, null, "Can't set message modify permission without read_all_messages permission",
                MAXErrorReason::WRITE_NEEDS_READ, [],
            ],
            'таймаут' => [
                0, null, 'cURL error 28: Operation timed out after 10001 milliseconds with 0 bytes received',
                MAXErrorReason::TIMEOUT, [],
            ],
            'пустой ответ' => [0, null, 'cURL error 52: Empty reply from server', MAXErrorReason::EMPTY_RESPONSE, []],
            'сбой сервера' => [502, null, 'Bad Gateway', MAXErrorReason::SERVER_ERROR, []],
            'внутренняя ошибка сервера' => [500, null, 'Internal Server Error', MAXErrorReason::SERVER_ERROR, []],
            'сервис недоступен' => [503, null, 'Service Unavailable', MAXErrorReason::SERVER_ERROR, []],
            'шлюз не дождался' => [504, null, 'Gateway Timeout', MAXErrorReason::TIMEOUT, []],
        ];
    }

    #[DataProvider('realErrors')]
    public function testRecognizesRealErrors(int $status, ?string $code, string $message, string $reason, array $params): void
    {
        $error = new MAXError($status, $code, $message);

        $this->assertSame($reason, $error->getReason());
        $this->assertSame($params, $error->getParams());
        $this->assertTrue($error->isRecognized());
    }

    public function testEveryReasonHasExample(): void
    {
        $covered = array_unique(array_column(self::realErrors(), 3));
        $expected = array_diff(MAXErrorReason::all(), [MAXErrorReason::UNKNOWN]);
        sort($covered);
        sort($expected);

        $this->assertSame($expected, $covered);
    }

    #[DataProvider('realErrors')]
    public function testTranslatesToPlainRussian(int $status, ?string $code, string $message): void
    {
        $text = MAXErrorTranslator::translate(new MAXError($status, $code, $message));

        $this->assertNotNull($text);
        $this->assertStringNotContainsString('{', $text);
        // Латиница допустима только в названии MAX и в адресах http(s)
        $latin = preg_replace('/\bMAX\b|https?:\/\/|«[^»]*»/u', '', $text);
        $this->assertDoesNotMatchRegularExpression('/[A-Za-z]/', $latin, $text);
        $this->assertMatchesRegularExpression('/^\p{Lu}.*\.$/u', $text);
    }

    public function testTranslationUsesParams(): void
    {
        $this->assertSame(
            'Текст сообщения длиннее 4000 символов (сейчас 4003). Сократите текст.',
            MAXErrorTranslator::translate(MAXError::fromMessage("Field 'text' size (4003) must be at most 4000"))
        );
        $this->assertSame(
            'MAX ограничил частоту запросов: не больше 30 запросов в секунду. Повторите попытку чуть позже.',
            MAXErrorTranslator::translate(MAXError::fromMessage('Too many requests. Limit is: 30 calls per second.'))
        );
        $this->assertSame(
            'MAX ограничил частоту запросов. Повторите попытку чуть позже.',
            MAXErrorTranslator::translate(MAXError::fromMessage('Too Many Requests', 429))
        );
    }

    public function testUnknownErrorHasNoTranslation(): void
    {
        // Общее правило длины — только про размер, не про любой верхний предел
        $this->assertSame(MAXErrorReason::UNKNOWN, MAXError::fromMessage('count must be at most 100', 400)->getReason());

        $error = MAXError::fromMessage('Something new happened', 400);

        $this->assertSame(MAXErrorReason::UNKNOWN, $error->getReason());
        $this->assertFalse($error->isRecognized());
        $this->assertNull(MAXErrorTranslator::translate($error));
    }

    public function testExceptionKeepsMAXErrorCode(): void
    {
        $request = $this->createRequest(MAXRequest::class, [
            new RequestException(
                'Client error',
                new Request('GET', 'me'),
                new Response(401, [], '{"code":"verify.token","message":"Invalid access_token"}')
            ),
        ]);

        try {
            $request->get('me');
            $this->fail('Expected MAXHttpException');
        } catch (MAXHttpException $e) {
            $this->assertSame('Invalid access_token', $e->getMessage());
            $this->assertSame(401, $e->getCode());
            $this->assertSame('verify.token', $e->getMAXError()->getErrorCode());
            $this->assertSame(401, $e->getMAXError()->getStatus());
            $this->assertSame(MAXErrorReason::TOKEN_INVALID, $e->getMAXError()->getReason());
        }
    }

    public function testOwnExceptionClassCanBeParsed(): void
    {
        $request = $this->createRequest(OwnExceptionMAXRequest::class, [
            new RequestException(
                'Client error',
                new Request('POST', 'messages'),
                new Response(400, [], '{"code":"bad.request","message":"Key: error.dialog.suspended, args: [1,]."}')
            ),
        ]);

        try {
            $request->postJSON('messages', ['text' => 'hi']);
            $this->fail('Expected OwnMAXException');
        } catch (OwnMAXException $e) {
            $error = MAXError::fromThrowable($e);
            $this->assertSame('bad.request', $error->getErrorCode());
            $this->assertSame(MAXErrorReason::DIALOG_SUSPENDED, $error->getReason());
        }
    }

    public function testParamWithBraceIsSubstituted(): void
    {
        $this->assertSame(
            'MAX не принял запрос: не заполнено обязательное поле «payload{0}».',
            MAXErrorTranslator::translate(MAXError::fromMessage('payload{0}: errors.required'))
        );
    }

    public function testParsesThrowableWithoutGuzzleException(): void
    {
        $error = MAXError::fromThrowable(new \RuntimeException('Account was blocked'));
        $this->assertSame(0, $error->getStatus());
        $this->assertSame(MAXErrorReason::BOT_BLOCKED, $error->getReason());

        $error = (new MAXHttpException('error.not.found', 404))->getMAXError();
        $this->assertSame(404, $error->getStatus());
        $this->assertSame(MAXErrorReason::NOT_FOUND, $error->getReason());
    }

    public function testErrorIsParsedOnceForNonRewindableBody(): void
    {
        $body = new NoSeekStream(Utils::streamFor('{"code":"bad.request","message":"Key: error.dialog.suspended, args: [1,]."}'));
        $request = $this->createRequest(MAXRequest::class, [
            new RequestException('Client error', new Request('POST', 'messages'), new Response(400, [], $body)),
        ]);

        try {
            $request->postJSON('messages', ['text' => 'hi']);
            $this->fail('Expected MAXHttpException');
        } catch (MAXHttpException $e) {
            $this->assertSame('Key: error.dialog.suspended, args: [1,].', $e->getMessage());
            $this->assertSame('bad.request', $e->getMAXError()->getErrorCode());
            $this->assertSame(MAXErrorReason::DIALOG_SUSPENDED, $e->getMAXError()->getReason());
        }
    }

    public function testTranslationIsOffByDefault(): void
    {
        try {
            $this->createRequest(MAXRequest::class, [self::tokenError()])->get('me');
            $this->fail('Expected MAXHttpException');
        } catch (MAXHttpException $e) {
            $this->assertSame('Invalid access_token', $e->getMessage());
        }
    }

    public function testTranslatesExceptionMessageWhenEnabled(): void
    {
        try {
            $this->createRequest(MAXRequest::class, [self::tokenError()], true)->get('me');
            $this->fail('Expected MAXHttpException');
        } catch (MAXHttpException $e) {
            $this->assertSame('Токен бота недействителен или отозван. Проверьте токен бота.', $e->getMessage());
            $this->assertSame(401, $e->getCode());
            $this->assertSame('Invalid access_token', $e->getMAXError()->getMessage());
            $this->assertSame(MAXErrorReason::TOKEN_INVALID, $e->getMAXError()->getReason());
        }
    }

    public function testUnknownErrorKeepsOriginalMessageWhenEnabled(): void
    {
        $request = $this->createRequest(MAXRequest::class, [
            new RequestException('Client error', new Request('GET', 'me'), new Response(400, [], '{"message":"Something new"}')),
        ], true);

        try {
            $request->get('me');
            $this->fail('Expected MAXHttpException');
        } catch (MAXHttpException $e) {
            $this->assertSame('Something new', $e->getMessage());
        }
    }

    public function testOwnExceptionClassGetsTranslatedMessageWhenEnabled(): void
    {
        try {
            $this->createRequest(OwnExceptionMAXRequest::class, [self::tokenError()], true)->get('me');
            $this->fail('Expected OwnMAXException');
        } catch (OwnMAXException $e) {
            $this->assertSame('Токен бота недействителен или отозван. Проверьте токен бота.', $e->getMessage());
            $this->assertSame('Invalid access_token', MAXError::fromThrowable($e)->getMessage());
        }
    }

    public function testSubclassWithoutParentConstructorStillWorks(): void
    {
        $exception = (new NoParentConstructorMAXRequest())->convert(self::tokenError());

        $this->assertInstanceOf(MAXHttpException::class, $exception);
        $this->assertSame('Invalid access_token', $exception->getMessage());
    }

    public function testSkipsNonStringMessageFields(): void
    {
        $error = MAXError::fromGuzzleException(new RequestException(
            'Client error',
            new Request('GET', 'me'),
            new Response(400, [], '{"message": {"text": "nested"}, "description": "Readable text"}')
        ));

        $this->assertSame('Readable text', $error->getMessage());
        $this->assertNull($error->getErrorCode());
    }

    public function testNetworkFailureIsRecognized(): void
    {
        $request = $this->createRequest(MAXRequest::class, [
            new ConnectException(
                'cURL error 28: Operation timed out after 10001 milliseconds with 0 bytes received',
                new Request('GET', 'me')
            ),
        ]);

        try {
            $request->get('me');
            $this->fail('Expected MAXHttpException');
        } catch (MAXHttpException $e) {
            $this->assertSame(0, $e->getMAXError()->getStatus());
            $this->assertNull($e->getMAXError()->getErrorCode());
            $this->assertSame(MAXErrorReason::TIMEOUT, $e->getMAXError()->getReason());
        }
    }

    /**
     * @param class-string<MAXRequest> $class
     */
    private function createRequest(string $class, array $responses, bool $translate_errors = false): MAXRequest
    {
        $client = new Client(['handler' => HandlerStack::create(new MockHandler($responses))]);

        return new $class('test_token', $client, $translate_errors);
    }

    private static function tokenError(): RequestException
    {
        return new RequestException(
            'Client error',
            new Request('GET', 'me'),
            new Response(401, [], '{"code":"verify.token","message":"Invalid access_token"}')
        );
    }
}

class OwnMAXException extends \RuntimeException
{
}

class NoParentConstructorMAXRequest extends MAXRequest
{
    public function __construct()
    {
    }

    public function convert(GuzzleException $e): \Throwable
    {
        return $this->toMAXHttpException($e);
    }
}

class OwnExceptionMAXRequest extends MAXRequest
{
    protected function createException(string $message, int $code, GuzzleException $original): \Throwable
    {
        return new OwnMAXException($message, $code, $original);
    }
}
