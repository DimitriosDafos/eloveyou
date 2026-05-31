@extends('layouts.app')
@section('title', __('chat.title'))
@push('styles')
<style>
    #messages { scroll-behavior: smooth; }
    .msg-out { background: #c0284a22; border-color: #c0284a44; }
    .msg-in  { background: #150f20; border-color: #2a1f35; }
</style>
@endpush
@section('content')
@php
    $me = auth()->user();
    $other = $chat->otherUser($me);
    $expired = $chat->isExpired();
@endphp

<div class="max-w-2xl mx-auto flex flex-col" style="height:calc(100vh - 120px)"
     x-data="chatApp({{ $chat->id }}, {{ $me->id }})" x-init="init()">

    {{-- Header --}}
    <div class="card p-4 flex items-center gap-3 mb-3 flex-shrink-0">
        <a href="{{ route('browse.index') }}" class="text-muted hover:text-text">←</a>
        <div class="w-9 h-9 rounded-full bg-primary/20 border border-primary/40 flex items-center justify-center font-bold text-primary text-sm">
            {{ strtoupper(substr($other->username, 0, 1)) }}
        </div>
        <div class="flex-1">
            <p class="font-semibold text-sm">{{ $other->username }}</p>
            <p class="text-xs text-muted">{{ $other->location_city }}</p>
        </div>
        @if($chat->photos_revealed)
        <span class="badge badge-green text-xs">{{ __('chat.photos_visible') }}</span>
        @else
        <span class="badge badge-muted text-xs">{{ __('chat.photos_hidden') }}</span>
        @endif
        @if(!$expired && $chat->expires_at)
        <span class="badge badge-amber text-xs" x-text="countdown"></span>
        @endif
    </div>

    {{-- Expired notice --}}
    @if($expired)
    <div class="card border-amber/30 p-4 text-center mb-3 flex-shrink-0">
        <p class="text-sm text-amber mb-3">{{ __('chat.chat_expired') }}</p>
        <a href="{{ route('chat.extend', $chat->id) }}" class="btn-amber text-sm">{{ __('chat.extend_btn') }}</a>
    </div>
    @endif

    {{-- Photos row (when revealed) --}}
    @if($chat->photos_revealed)
    <div class="card p-3 flex gap-3 overflow-x-auto mb-3 flex-shrink-0">
        <div>
            <p class="text-xs text-muted mb-1">{{ $me->username }}</p>
            <div class="flex gap-2">
                @foreach($me->photos->where('status','approved') as $p)
                <img src="{{ $p->url }}" class="h-16 w-16 object-cover rounded-md">
                @endforeach
            </div>
        </div>
        <div class="border-l border-border pl-3">
            <p class="text-xs text-muted mb-1">{{ $other->username }}</p>
            <div class="flex gap-2">
                @foreach($other->photos->where('status','approved') as $p)
                <img src="{{ $p->url }}" class="h-16 w-16 object-cover rounded-md">
                @endforeach
            </div>
        </div>
    </div>
    @endif

    {{-- Messages --}}
    <div id="messages" class="flex-1 overflow-y-auto space-y-2 pr-1 mb-3">
        @foreach($chat->messages as $msg)
        <div class="flex {{ $msg->sender_id === $me->id ? 'justify-end' : 'justify-start' }}">
            <div class="max-w-xs lg:max-w-sm px-4 py-2.5 rounded-xl border text-sm leading-relaxed
                {{ $msg->sender_id === $me->id ? 'msg-out' : 'msg-in' }}">
                {{ $msg->body }}
                <span class="text-[10px] text-muted ml-2 float-right mt-1">{{ $msg->created_at->format('H:i') }}</span>
            </div>
        </div>
        @endforeach
        {{-- Real-time messages injected here --}}
        <template x-for="msg in newMessages" :key="msg.id">
            <div :class="msg.sender_id === myId ? 'flex justify-end' : 'flex justify-start'">
                <div class="max-w-xs px-4 py-2.5 rounded-xl border text-sm leading-relaxed"
                     :class="msg.sender_id === myId ? 'msg-out' : 'msg-in'"
                     x-text="msg.body"></div>
            </div>
        </template>
        <div id="bottom"></div>
    </div>

    {{-- Input --}}
    @if(!$expired)
    <form method="POST" action="{{ route('chat.message', $chat->id) }}" class="flex-shrink-0"
          @submit.prevent="submitMessage($event)">
        @csrf
        <div class="flex gap-2">
            <input name="body" class="input flex-1" type="text" maxlength="1000"
                   placeholder="{{ __('chat.type_message') }}" autocomplete="off" x-ref="msgInput">
            <button type="submit" class="btn-primary px-4">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                </svg>
            </button>
        </div>
    </form>
    @endif

</div>

@push('scripts')
<script>
function chatApp(chatId, myId) {
    return {
        chatId, myId,
        newMessages: [],
        countdown: '',
        expiresAt: '{{ $chat->expires_at?->toISOString() }}',

        init() {
            this.scrollBottom();
            this.startCountdown();

            window.Echo.private(`chat.${this.chatId}`)
                .listen('.message.sent', (e) => {
                    if (e.message.sender_id !== this.myId) {
                        this.newMessages.push(e.message);
                        this.$nextTick(() => this.scrollBottom());
                    }
                });
        },

        scrollBottom() {
            this.$nextTick(() => {
                const el = document.getElementById('bottom');
                if (el) el.scrollIntoView({ behavior: 'smooth' });
            });
        },

        startCountdown() {
            if (!this.expiresAt) return;
            const update = () => {
                const diff = new Date(this.expiresAt) - new Date();
                if (diff <= 0) { this.countdown = '{{ __('chat.expired_label') }}'; return; }
                const h = Math.floor(diff/3600000), m = Math.floor((diff%3600000)/60000);
                this.countdown = `${h}h ${m}m {{ __('chat.left') }}`;
                setTimeout(update, 30000);
            };
            update();
        },

        async submitMessage(e) {
            const form = e.target;
            const input = this.$refs.msgInput;
            const body = input.value.trim();
            if (!body) return;

            this.newMessages.push({ id: Date.now(), sender_id: this.myId, body });
            input.value = '';
            this.scrollBottom();

            const data = new FormData(form);
            data.set('body', body);
            await fetch(form.action, { method: 'POST', body: data, headers: { 'X-Requested-With': 'XMLHttpRequest' } });
        }
    }
}
</script>
@endpush
@endsection
