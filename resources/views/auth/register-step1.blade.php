@extends('layouts.guest')
@section('title', __('auth.create_account'))
@section('content')
<div class="card p-8">
    <h1 class="text-xl font-bold mb-1">{{ __('auth.create_account') }}</h1>
    <p class="text-muted text-sm mb-6">{{ __('auth.register_sub') }}</p>

    <form method="POST" action="{{ route('register.post') }}" class="space-y-4" x-data>
        @csrf

        <div>
            <label>{{ __('auth.username') }} <span class="text-primary">*</span></label>
            <input class="input" type="text" name="username" value="{{ old('username') }}" placeholder="{{ __('auth.username_placeholder') }}" required minlength="3" maxlength="30" pattern="[a-zA-Z0-9_\-]+">
            <p class="text-xs text-muted mt-1">{{ __('auth.username_hint') }}</p>
        </div>

        <div>
            <label>{{ __('auth.real_name') }} <span class="text-primary">*</span></label>
            <input class="input" type="text" name="real_name" value="{{ old('real_name') }}" placeholder="{{ __('auth.real_name_placeholder') }}" required>
            <p class="text-xs text-muted mt-1 flex items-start gap-1">
                <span class="text-amber mt-0.5">🔒</span>
                {{ __('auth.real_name_private') }}
            </p>
        </div>

        <div>
            <label>{{ __('auth.phone') }} <span class="text-primary">*</span></label>
            <input class="input" type="tel" name="phone" value="{{ old('phone') }}" placeholder="+49 123 456789" required>
            <p class="text-xs text-muted mt-1">{{ __('auth.phone_hint') }}</p>
        </div>

        <div>
            <label>{{ __('auth.password') }} <span class="text-primary">*</span></label>
            <input class="input" type="password" name="password" placeholder="••••••••" required minlength="8">
        </div>

        <div>
            <label>{{ __('auth.password_confirm') }} <span class="text-primary">*</span></label>
            <input class="input" type="password" name="password_confirmation" placeholder="••••••••" required>
        </div>

        <div class="card p-4 space-y-3 border-amber/30">
            <p class="text-sm font-semibold text-amber">{{ __('auth.age_gate_title') }}</p>
            <p class="text-xs text-muted">{{ __('auth.age_gate_text') }}</p>
            <label class="flex items-start gap-3 cursor-pointer">
                <input type="checkbox" name="age_confirm" class="accent-primary mt-1 flex-shrink-0" required>
                <span class="text-sm">{{ __('auth.age_confirm_label') }}</span>
            </label>
        </div>

        <button type="submit" class="btn-primary w-full text-center">{{ __('auth.create_account_btn') }}</button>
    </form>

    <p class="text-center text-sm text-muted mt-6">
        {{ __('auth.have_account') }}
        <a href="{{ route('login') }}" class="text-primary hover:underline">{{ __('auth.sign_in_here') }}</a>
    </p>
</div>
@endsection
