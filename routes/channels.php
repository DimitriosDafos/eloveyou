<?php
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('chat.{chatId}', function ($user, $chatId) {
    $chat = \App\Models\Chat::find($chatId);
    if (!$chat) return false;
    return in_array($user->id, [$chat->requester_id, $chat->acceptor_id]);
});
