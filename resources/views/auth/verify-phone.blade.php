@extends('layouts.guest')
@section('title', __('auth.verify_phone'))
@section('content')
<div class="card p-8 text-center">
    <div class="w-14 h-14 rounded-full bg-primary/10 border border-primary/30 flex items-center justify-center mx-auto mb-4">
        <svg class="w-7 h-7 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
        </svg>
    </div>

    <h1 class="text-xl font-bold mb-1">{{ __('auth.verify_phone') }}</h1>
    <p class="text-muted text-sm mb-6">{{ __('auth.verify_sub') }}</p>

    <form method="POST" action="{{ route('register.verify.post') }}" class="space-y-4" x-data>
        @csrf
        <div>
            <label>{{ __('auth.enter_code') }}</label>
            <input class="input text-center text-2xl tracking-widest font-bold" type="text"
                   name="code" placeholder="000000" maxlength="6" minlength="6"
                   pattern="\d{6}" inputmode="numeric" autocomplete="one-time-code" required autofocus>
        </div>
        <button type="submit" class="btn-primary w-full text-center">{{ __('auth.verify_btn') }}</button>
    </form>

    <div class="mt-4 text-sm text-muted">
        {{ __('auth.no_code') }}
        <form method="POST" action="{{ route('register.resend') }}" class="inline">
            @csrf
            <button class="text-primary hover:underline">{{ __('auth.resend_code') }}</button>
        </form>
    </div>

    <p class="text-xs text-muted mt-4">{{ __('auth.code_expires') }}</p>
</div>
@endsection
