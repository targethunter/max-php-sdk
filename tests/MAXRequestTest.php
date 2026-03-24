<?php

declare(strict_types=1);

namespace TH\MAX\Tests;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use TH\MAX\Client\Request\MAXRequest;
use TH\MAX\Exceptions\MAXHttpException;

class MAXRequestTest extends TestCase
{
    private function createRequest(array $responses): MAXRequest
    {
        $mock = new MockHandler($responses);
        $handler = HandlerStack::create($mock);
        $client = new Client(['handler' => $handler]);

        return new MAXRequest('test_token', $client);
    }

    public function testGetSuccess(): void
    {
        $request = $this->createRequest([
            new Response(200, [], '{"success": true}'),
        ]);

        $response = $request->get('messages');
        $body = json_decode($response->getBody()->getContents(), true);

        $this->assertTrue($body['success']);
    }

    public function testPostJSONSuccess(): void
    {
        $request = $this->createRequest([
            new Response(200, [], '{"mid": "abc123"}'),
        ]);

        $response = $request->postJSON('messages', ['text' => 'hello']);
        $body = json_decode($response->getBody()->getContents(), true);

        $this->assertEquals('abc123', $body['mid']);
    }

    public function testThrowsMAXHttpExceptionOnError(): void
    {
        $request = $this->createRequest([
            new RequestException(
                'Server error',
                new Request('GET', 'test'),
                new Response(500, [], '{"message": "Internal Server Error"}')
            ),
        ]);

        $this->expectException(MAXHttpException::class);
        $this->expectExceptionCode(500);
        $this->expectExceptionMessage('Internal Server Error');

        $request->get('messages');
    }

    public function testExceptionExtractsMessageFromErrorField(): void
    {
        $request = $this->createRequest([
            new RequestException(
                'Bad request',
                new Request('POST', 'test'),
                new Response(400, [], '{"error": {"message": "Invalid parameter"}}')
            ),
        ]);

        try {
            $request->postJSON('messages', []);
            $this->fail('Expected MAXHttpException');
        } catch (MAXHttpException $e) {
            $this->assertEquals('Invalid parameter', $e->getMessage());
            $this->assertEquals(400, $e->getCode());
            $this->assertNotNull($e->getOriginalException());
            $this->assertNotNull($e->getPrevious());
        }
    }

    public function testExceptionFallsBackToRawBody(): void
    {
        $request = $this->createRequest([
            new RequestException(
                'Error',
                new Request('GET', 'test'),
                new Response(502, [], 'Bad Gateway')
            ),
        ]);

        try {
            $request->get('test');
            $this->fail('Expected MAXHttpException');
        } catch (MAXHttpException $e) {
            $this->assertEquals('Bad Gateway', $e->getMessage());
            $this->assertEquals(502, $e->getCode());
        }
    }

    public function testAccessTokenPassedInAuthorizationHeader(): void
    {
        $historyContainer = [];
        $mock = new MockHandler([
            new Response(200, [], '{}'),
        ]);
        $handler = HandlerStack::create($mock);
        $handler->push(\GuzzleHttp\Middleware::history($historyContainer));
        $client = new Client(['handler' => $handler]);

        $request = new MAXRequest('my_secret_token', $client);
        $request->get('bots/me', ['foo' => 'bar']);

        $this->assertCount(1, $historyContainer);

        $sentRequest = $historyContainer[0]['request'];

        // Токен в заголовке Authorization
        $this->assertEquals('my_secret_token', $sentRequest->getHeaderLine('Authorization'));

        // Токен НЕ в query
        $query = $sentRequest->getUri()->getQuery();
        $this->assertStringNotContainsString('access_token', $query);
        $this->assertStringContainsString('foo=bar', $query);
    }

    public function testCreateExceptionCanBeOverridden(): void
    {
        $customRequest = new class('token') extends MAXRequest {
            public function __construct(string $token)
            {
                $mock = new MockHandler([
                    new RequestException(
                        'Error',
                        new Request('GET', 'test'),
                        new Response(422, [], '{"message": "Validation failed"}')
                    ),
                ]);
                $handler = HandlerStack::create($mock);
                parent::__construct($token, new Client(['handler' => $handler]));
            }

            protected function createException(string $message, int $code, \GuzzleHttp\Exception\GuzzleException $original): \Throwable
            {
                return new \RuntimeException("Custom: $message", $code, $original);
            }
        };

        try {
            $customRequest->get('test');
            $this->fail('Expected RuntimeException');
        } catch (\RuntimeException $e) {
            $this->assertEquals('Custom: Validation failed', $e->getMessage());
            $this->assertEquals(422, $e->getCode());
        }
    }

    public function testDeleteJSONSuccess(): void
    {
        $request = $this->createRequest([
            new Response(200, [], '{"success": true}'),
        ]);

        $response = $request->deleteJSON('messages/abc123');
        $body = json_decode($response->getBody()->getContents(), true);

        $this->assertTrue($body['success']);
    }

    public function testPutJSONSuccess(): void
    {
        $request = $this->createRequest([
            new Response(200, [], '{"success": true}'),
        ]);

        $response = $request->putJSON('chats/123', ['title' => 'New Title']);
        $body = json_decode($response->getBody()->getContents(), true);

        $this->assertTrue($body['success']);
    }

    public function testPatchJSONSuccess(): void
    {
        $request = $this->createRequest([
            new Response(200, [], '{"user_id": 1}'),
        ]);

        $response = $request->patchJSON('me', ['name' => 'Bot']);
        $body = json_decode($response->getBody()->getContents(), true);

        $this->assertEquals(1, $body['user_id']);
    }
}
