<?php

namespace TH\MAX\Client\Modules\Chats;

use TH\MAX\Client\DTO\Chats\Chat;
use TH\MAX\Client\DTO\Chats\ChatMember;
use TH\MAX\Client\DTO\Chats\Response\ChatListResponse;
use TH\MAX\Client\DTO\Chats\Response\ChatMemberListResponse;
use TH\MAX\Client\DTO\Messages\Response\MessageResponse;
use TH\MAX\Client\DTO\ResultResponse;
use TH\MAX\Client\Modules\CommonModule;
use TH\MAX\Interfaces\MAXRequestInterface;

class Chats extends CommonModule
{
    public function __construct(MAXRequestInterface $request)
    {
        parent::__construct($request);
    }

    public function getAll(int $count = 50, ?int $marker = null): ChatListResponse
    {
        $response = $this->get('/chats', [
            'count' => $count,
            'marker' => $marker
        ]);

        return new ChatListResponse($response);
    }

    public function getByLink(string $chat_link): Chat
    {
        return new Chat(
            $this->get('/chats/' . $chat_link)
        );
    }

    public function getById(int $chat_id): Chat
    {
        return new Chat(
            $this->get('/chats/' . $chat_id)
        );
    }

    public function update(
        int $chat_id,
        ?array $icon = null,
        ?string $title = null,
        ?string $pin = null,
        ?bool $notify = true
    ): Chat {
        return new Chat(
            $this->patch('/chats/' . $chat_id, [
                'icon' => $icon,
                'title' => $title,
                'pin' => $pin,
                'notify' => $notify
            ])
        );
    }

    public function remove(int $chat_id): ResultResponse
    {
        return new ResultResponse(
            $this->delete('/chats/' . $chat_id)
        );
    }

    public function sendAction(int $chat_id, string $action): ResultResponse
    {
        return new ResultResponse(
            $this->post('/chats/' . $chat_id . '/actions', [
                'action' => $action
            ])
        );
    }

    public function getPinnedMessage(int $chat_id): MessageResponse
    {
        return new MessageResponse(
            $this->get('/chats/' . $chat_id . '/pin')
        );
    }

    public function pinMessage(
        int $chat_id,
        string $message_id,
        bool $notify = true
    ): ResultResponse {
        return new ResultResponse(
            $this->put('/chats/' . $chat_id . '/pin', [
                'message_id' => $message_id,
                'notify' => $notify
            ])
        );
    }

    public function deletePinnedMessage(int $chat_id): ResultResponse
    {
        return new ResultResponse(
            $this->delete('/chats/' . $chat_id . '/pin')
        );
    }

    public function getBotMembership(int $chat_id): ChatMember
    {
        return new ChatMember(
            $this->get('/chats/' . $chat_id . '/members/me')
        );
    }

    public function deleteBot(int $chat_id): ResultResponse
    {
        return new ResultResponse(
            $this->delete('/chats/' . $chat_id . '/members/me')
        );
    }

    public function getAdmins(int $chat_id): ChatMemberListResponse
    {
        $response = $this->get('/chats/' . $chat_id . '/members/admins');

        return new ChatMemberListResponse($response);
    }

    public function assignAdmins(
        int $chat_id,
        array $admins,
        ?int $marker = null
    ): ResultResponse {
        return new ResultResponse(
            $this->post('/chats/' . $chat_id . '/members/admins', [
                'admins' => $admins,
                'marker' => $marker
            ])
        );
    }

    public function revokeAdmin(int $chat_id, int $user_id): ResultResponse
    {
        return new ResultResponse(
            $this->delete('/chats/' . $chat_id . '/members/admins/' . $user_id)
        );
    }

    public function getMembers(
        int $chat_id,
        ?array $user_ids = null,
        ?int $marker = null,
        int $count = 20
    ): ChatMemberListResponse {
        $response = $this->get('/chats/' . $chat_id . '/members', [
            'user_ids' => $user_ids,
            'marker' => $marker,
            'count' => $count
        ]);

        return new ChatMemberListResponse($response);
    }

    public function addMembers(int $chat_id, array $user_ids): ResultResponse
    {
        return new ResultResponse(
            $this->post('/chats/' . $chat_id . '/members', [
                'user_ids' => $user_ids
            ])
        );
    }

    public function deleteMember(
        int $chat_id,
        int $user_id,
        ?bool $block = null
    ): ResultResponse {
        return new ResultResponse(
            $this->delete('/chats/' . $chat_id . '/members', [
                'user_id' => $user_id,
                'block' => $block
            ])
        );
    }
}