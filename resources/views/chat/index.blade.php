@extends('layouts.app')
@section('title', __('chat.my_chats'))
@section('content')
<div class="max-w-2xl mx-auto">
    <h1 class="text-xl font-bold mb-6">{{ __('chat.my_chats') }}</h1>

    @forelse($chats as $chat)
    @php $other = $chat->otherUser(auth()->user()); $last = $chat->messages->first(); @endphp
    <a href="{{ route('chat.show', $chat->id) }}" class="card p-4 flex items-center gap-4 mb-3 hover:border-primary/50 transition-colors block">
        <div class="w-11 h-11 rounded-full flex-shrink-0 flex items-center justify-center font-bold text-primary bg-primary/10 border border-primary/30">
            {{ strtoupper(substr($other->username, 0, 1)) }}
        </div>
        <div class="flex-1 min-w-0">
            <div class="flex items-center justify-between">
                <p class="font-semibold text-sm">{{ $other->username }}</p>
                <span class="text-xs text-muted">{{ $last?->created_at->diffForHumans() ?? $chat->created_at->diffForHumans() }}</span>
            </div>
            <p class="text-xs text-muted truncate mt-0.5">{{ $last?->body ?? __('chat.no_messages_yet') }}</p>
        </div>
        <div class="flex flex-col items-end gap-1">
            @if($chat->isExpired())
            <span class="badge badge-red">{{ __('chat.expired_label') }}</span>
            @elseif($chat->photos_revealed)
            <span class="badge badge-green">{{ __('chat.photos_visible') }}</span>
            @else
            <span class="badge badge-amber">{{ __('chat.active') }}</span>
            @endif
        </div>
    </a>
    @empty
    <div class="card p-12 text-center">
        <p class="text-muted text-sm">{{ __('chat.no_chats') }}</p>
        <a href="{{ route('browse.index') }}" class="btn-primary mt-4 inline-block">{{ __('browse.discover') }}</a>
    </div>
    @endforelse
</div>
@endsection
