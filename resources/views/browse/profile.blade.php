@extends('layouts.app')
@section('title', $profile->username)
@section('content')
<div class="max-w-2xl mx-auto" x-data="{ sending: false, reportOpen: false }">

    <a href="{{ route('browse.index') }}" class="text-muted text-sm hover:text-text flex items-center gap-1 mb-6">
        ← {{ __('browse.back') }}
    </a>

    <div class="card overflow-hidden">

        {{-- Photos --}}
        <div class="relative bg-surface" style="height:280px">
            @if($profile->photos->where('status','approved')->count() > 0)
            <div x-data="{ active: 0 }" class="relative h-full">
                @foreach($profile->photos->where('status','approved') as $i => $photo)
                <img src="{{ $photo->url }}" alt=""
                     x-show="active === {{ $i }}"
                     class="absolute inset-0 w-full h-full object-cover blur-2xl scale-110 opacity-50">
                @endforeach
                <div class="absolute inset-0 flex items-center justify-center backdrop-blur-sm">
                    <div class="text-center">
                        <div class="w-20 h-20 rounded-full bg-primary/20 border-2 border-primary/50 flex items-center justify-center text-3xl font-bold text-primary mx-auto mb-2">
                            {{ strtoupper(substr($profile->username, 0, 1)) }}
                        </div>
                        <p class="text-xs text-muted">{{ __('browse.photos_blurred') }}</p>
                    </div>
                </div>
                @if($profile->photos->where('status','approved')->count() > 1)
                <div class="absolute bottom-3 left-1/2 -translate-x-1/2 flex gap-1">
                    @foreach($profile->photos->where('status','approved') as $i => $p)
                    <button @click="active={{ $i }}" class="w-1.5 h-1.5 rounded-full transition-colors"
                            :class="active==={{ $i }} ? 'bg-white' : 'bg-white/40'"></button>
                    @endforeach
                </div>
                @endif
            </div>
            @else
            <div class="h-full flex items-center justify-center">
                <div class="w-20 h-20 rounded-full bg-primary/20 border-2 border-primary/50 flex items-center justify-center text-3xl font-bold text-primary">
                    {{ strtoupper(substr($profile->username, 0, 1)) }}
                </div>
            </div>
            @endif
        </div>

        {{-- Info --}}
        <div class="p-6">
            <div class="flex items-start justify-between mb-4">
                <div>
                    <h1 class="text-xl font-bold">{{ $profile->username }}</h1>
                    <p class="text-muted text-sm">{{ $profile->age }} · {{ $profile->location_city }}, {{ $profile->location_region }}</p>
                </div>
                <div class="flex gap-2">
                    <form method="POST" action="{{ route('user.block', $profile->id) }}">
                        @csrf
                        <button class="btn-ghost text-xs px-2 py-1">{{ __('browse.block') }}</button>
                    </form>
                    <button @click="reportOpen=!reportOpen" class="btn-ghost text-xs px-2 py-1 text-red-400 border-red-900/50">{{ __('browse.report') }}</button>
                </div>
            </div>

            {{-- Practices --}}
            <div class="flex flex-wrap gap-1.5 mb-4">
                @foreach($profile->practices as $p)
                <span class="badge badge-muted">{{ $p->label(app()->getLocale()) }}</span>
                @endforeach
            </div>

            {{-- Looking for --}}
            <p class="text-xs text-muted mb-4">
                {{ __('browse.looking_for') }}: {{ collect($profile->looking_for)->map(fn($l)=>__('profile.looking_'.$l))->join(', ') }}
            </p>

            {{-- Bio --}}
            <div class="border-t border-border pt-4 mb-6">
                <p class="text-sm leading-relaxed whitespace-pre-line">{{ $profile->bio }}</p>
            </div>

            {{-- Action --}}
            @if($existingMatch)
                @if($existingMatch->status === 'pending' && $existingMatch->requester_id === auth()->id())
                <div class="card bg-surface p-4 text-center text-sm text-muted">{{ __('match.already_sent_notice') }}</div>
                @elseif($existingMatch->status === 'accepted')
                <a href="{{ route('chat.show', $existingMatch->chat->id) }}" class="btn-primary w-full text-center block">{{ __('match.go_to_chat') }}</a>
                @else
                <div class="card bg-surface p-4 text-center text-sm text-muted">{{ __('match.status_'.$existingMatch->status) }}</div>
                @endif
            @else
            <div x-show="!sending">
                <button @click="sending=true" class="btn-primary w-full text-center">{{ __('match.send_message') }}</button>
            </div>
            <form x-show="sending" x-transition method="POST" action="{{ route('match.send', $profile->id) }}" class="space-y-3">
                @csrf
                <textarea class="input" name="message" rows="4" maxlength="500" minlength="10" required
                          placeholder="{{ __('match.message_placeholder') }}"></textarea>
                <p class="text-xs text-muted">{{ __('match.first_message_hint') }}</p>
                @if(session('filter_message'))
                <div class="card border-amber/30 p-4 text-sm text-muted leading-relaxed">
                    {{ __('match.filter_blocked_msg') }}
                </div>
                @endif
                <div class="flex gap-2">
                    <button type="submit" class="btn-primary flex-1 text-center">{{ __('match.send_btn') }}</button>
                    <button type="button" @click="sending=false" class="btn-ghost">{{ __('ui.cancel') }}</button>
                </div>
            </form>
            @endif
        </div>
    </div>

    {{-- Report form --}}
    <div x-show="reportOpen" x-transition class="card p-6 mt-4">
        <h3 class="font-semibold mb-4 text-sm">{{ __('browse.report_title') }}</h3>
        <form method="POST" action="{{ route('user.report', $profile->id) }}" class="space-y-3">
            @csrf
            <select class="input text-sm" name="type" required>
                <option value="profile">{{ __('browse.report_profile') }}</option>
                <option value="photo">{{ __('browse.report_photo') }}</option>
                <option value="message">{{ __('browse.report_message') }}</option>
            </select>
            <textarea class="input text-sm" name="description" rows="3" maxlength="500" placeholder="{{ __('browse.report_desc') }}"></textarea>
            <button type="submit" class="btn-primary text-sm">{{ __('browse.submit_report') }}</button>
        </form>
    </div>
</div>
@endsection
