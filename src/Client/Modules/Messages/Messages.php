<?php

namespace TH\MAX\Client\Modules\Messages;

use TH\MAX\Client\DTO\Messages\Collection\MessageCollection;
use TH\MAX\Client\DTO\Messages\MessageDTO;
use TH\MAX\Client\DTO\Messages\Response\MessageListResponse;
use TH\MAX\Client\DTO\Messages\VideoInfoDTO;
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

        $response['items'] = MessageCollection::fromArray($response['messages']);

        return new MessageListResponse($response);
    }

    public function send(
        ?int $text = null,
        ?array $attachments = null,
        ?array $link = null,
        bool $notify = true,
        ?string $format = null
    ): MessageDTO {
        return new MessageDTO(
            $this->post('/messages', [
                'text' => $text,
                'attachments' => $attachments,
                'link' => $link,
                'notify' => $notify,
                'format' => $format,
            ])
        );
    }

    public function update(
        string $message_id,
        ?int $text = null,
        ?array $attachments = null,
        ?array $link = null,
        bool $notify = true,
        ?string $format = null
    ): ResultResponse {
        return new ResultResponse(
            $this->put('/messages', [
                'message_id' => $message_id,
                'text' => $text,
                'attachments' => $attachments,
                'link' => $link,
                'notify' => $notify,
                'format' => $format,
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

    public function getMessage(string $message_id): MessageDTO
    {
        return new MessageDTO(
            $this->get('/messages/' . $message_id, [
                'message_id' => $message_id,
            ])
        );
    }

    public function getVideoInfo(string $video_token): VideoInfoDTO
    {
        return new VideoInfoDTO(
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