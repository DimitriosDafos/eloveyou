@extends('layouts.guest')
@section('title', __('auth.login'))
@section('content')
<div class="card p-8">
    <h1 class="text-xl font-bold mb-1">{{ __('auth.welcome_back') }}</h1>
    <p class="text-muted text-sm mb-6">{{ __('auth.login_sub') }}</p>

    <form method="POST" action="{{ route('login.post') }}" class="space-y-4">
        @csrf
        <div>
            <label>{{ __('auth.phone') }}</label>
            <input class="input" type="tel" name="phone" value="{{ old('phone') }}" placeholder="+49 123 456789" required autofocus>
        </div>
        <div>
            <label>{{ __('auth.password') }}</label>
            <input class="input" type="password" name="password" placeholder="••••••••" required>
        </div>
        <div class="flex items-center gap-2">
            <input type="checkbox" name="remember" id="remember" class="accent-primary">
            <label for="remember" class="!mb-0 text-sm text-muted cursor-pointer">{{ __('auth.remember_me') }}</label>
        </div>
        <button type="submit" class="btn-primary w-full text-center">{{ __('auth.sign_in') }}</button>
    </form>

    <p class="text-center text-sm text-muted mt-6">
        {{ __('auth.no_account') }}
        <a href="{{ route('register') }}" class="text-primary hover:underline">{{ __('auth.register_here') }}</a>
    </p>
</div>
@endsection
