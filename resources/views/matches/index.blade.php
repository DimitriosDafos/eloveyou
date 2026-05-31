@extends('layouts.app')
@section('title', __('match.title'))
@section('content')
<div class="max-w-2xl mx-auto">
    <h1 class="text-xl font-bold mb-6">{{ __('match.title') }}</h1>

    {{-- Pending incoming --}}
    <div class="mb-8">
        <h2 class="text-sm font-semibold text-muted uppercase tracking-wider mb-3">
            {{ __('match.incoming') }}
            @if($pending->count()) <span class="badge badge-red ml-2">{{ $pending->count() }}</span> @endif
        </h2>
        @forelse($pending as $match)
        <div class="card p-4 mb-3 flex items-start gap-4">
            <div class="w-10 h-10 rounded-full bg-primary/20 border border-primary/40 flex items-center justify-center font-bold text-primary flex-shrink-0">
                {{ strtoupper(substr($match->requester->username, 0, 1)) }}
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between mb-1">
                    <a href="{{ route('profile.show', $match->requester->username) }}" class="font-semibold text-sm hover:text-primary transition-colors">
                        {{ $match->requester->username }}
                    </a>
                    <span class="text-xs text-muted">{{ $match->created_at->diffForHumans() }}</span>
                </div>
                <p class="text-sm text-muted leading-relaxed mb-3">{{ $match->opening_message }}</p>
                <div class="flex gap-2">
                    <form method="POST" action="{{ route('match.accept', $match->id) }}">
                        @csrf
                        <button class="btn-primary text-sm px-4 py-2">{{ __('match.accept') }}</button>
                    </form>
                    <form method="POST" action="{{ route('match.decline', $match->id) }}">
                        @csrf
                        <button class="btn-ghost text-sm px-4 py-2">{{ __('match.decline') }}</button>
                    </form>
                </div>
            </div>
        </div>
        @empty
        <div class="card p-6 text-center text-muted text-sm">{{ __('match.no_incoming') }}</div>
        @endforelse
    </div>

    {{-- Sent --}}
    <div>
        <h2 class="text-sm font-semibold text-muted uppercase tracking-wider mb-3">{{ __('match.sent') }}</h2>
        @forelse($sent as $match)
        <div class="card p-4 mb-3 flex items-start gap-4 opacity-70">
            <div class="w-10 h-10 rounded-full bg-surface border border-border flex items-center justify-center font-bold text-muted flex-shrink-0">
                {{ strtoupper(substr($match->acceptor->username, 0, 1)) }}
            </div>
            <div class="flex-1">
                <div class="flex items-center justify-between mb-1">
                    <a href="{{ route('profile.show', $match->acceptor->username) }}" class="font-semibold text-sm hover:text-primary transition-colors">
                        {{ $match->acceptor->username }}
                    </a>
                    <span class="badge badge-muted">{{ __('match.status_pending') }}</span>
                </div>
                <p class="text-xs text-muted">{{ $match->created_at->diffForHumans() }}</p>
            </div>
        </div>
        @empty
        <div class="card p-6 text-center text-muted text-sm">{{ __('match.no_sent') }}</div>
        @endforelse
    </div>
</div>
@endsection
