<?php

declare(strict_types=1);

namespace TH\MAX\Tests;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use TH\MAX\Client\Modules\Messages\Messages;
use TH\MAX\Client\Request\MAXRequest;

class MessagesTest extends TestCase
{
    public function testAnswerCallbackSendsCallbackIdInQueryOnly(): void
    {
        $mock = new MockHandler([
            new Response(200, [], json_encode(['success' => true])),
        ]);
        $client = new Client(['handler' => HandlerStack::create($mock)]);
        $request = new MAXRequest('token', $client);

        (new Messages($request))->answerCallback('cb-1', null, 'txt');

        $sent = $mock->getLastRequest();
        $body = json_decode((string) $sent->getBody(), true);

        $this->assertStringContainsString('callback_id=cb-1', $sent->getUri()->getQuery());
        $this->assertArrayNotHasKey('callback_id', $body);
        $this->assertSame('txt', $body['notification']);
    }
}
