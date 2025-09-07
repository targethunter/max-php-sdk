<?php

namespace TH\MAX\Client\Modules\Messages;

use TH\MAX\Client\DTO\Messages\Message;
use TH\MAX\Client\DTO\Messages\Response\MessageListResponse;
use TH\MAX\Client\DTO\Messages\VideoInfo;
use TH\MAX\Client\DTO\ResultResponse;
use TH\MAX\Client\Modules\CommonModule;
use TH\MAX\Interfaces\MAXRequestInterface;

class Messages extends CommonModule
{
    public function __construct(MAXRequestInterface $request)
    {
        parent::__construct($request);
    }

    public function getAll(
        ?int $chat_id = null,
        ?array $message_ids = null,
        ?int $from = null,
        ?int $to = null,
        int $count = 50
    ): MessageListResponse {
        $response = $this->get('/messages', [
            'chat_id' => $chat_id,
            'message_ids' => $message_ids,
            'from' => $from,
            'to' => $to,
            'count' => $count,
        ]);

        return new MessageListResponse($response);
    }

    public function send(
        ?int $user_id = null,
        ?int $chat_id = null,
        ?string $text = null,
        ?array $attachments = null,
        ?bool $disable_link_preview = null,
        ?array $link = null,
        bool $notify = true,
        ?string $format = null
    ): Message {
        return new Message(
            $this->post('/messages', [
                'text' => $text,
                'attachments' => $attachments,
                'link' => $link,
                'notify' => $notify,
                'format' => $format,
            ], [
                'user_id' => $user_id,
                'chat_id' => $chat_id,
                'disable_link_preview' => $disable_link_preview,
            ])['message'] ?? null,
        );
    }

    public function update(
        string $message_id,
        ?string $text = null,
        ?array $attachments = null,
        ?array $link = null,
        bool $notify = true,
        ?string $format = null
    ): ResultResponse {
        return new ResultResponse(
            $this->put('/messages', [
                'text' => $text,
                'attachments' => $attachments,
                'link' => $link,
                'notify' => $notify,
                'format' => $format,
            ], [
                'message_id' => $message_id,
            ])
        );
    }

    public function remove(string $message_id): ResultResponse
    {
        return new ResultResponse(
            $this->delete('/messages', [
                'message_id' => $message_id,
            ])
        );
    }

    public function getById(string $message_id): Message
    {
        return new Message(
            $this->get('/messages/' . $message_id, [
                'message_id' => $message_id,
            ])
        );
    }

    public function getVideoInfo(string $video_token): VideoInfo
    {
        return new VideoInfo(
            $this->get('/videos/' . $video_token)
        );
    }

    public function answerCallback(
        string $callback_id,
        ?array $message = null,
        ?string $notification = null
    ): ResultResponse {
        return new ResultResponse(
            $this->post('/answers', [
                'callback_id' => $callback_id,
                'message' => $message,
                'notification' => $notification,
            ])
        );
    }
}