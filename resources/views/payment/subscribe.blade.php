@extends('layouts.app')
@section('title', __('payment.subscribe_title'))
@section('content')
<div class="max-w-2xl mx-auto">
    <div class="text-center mb-8">
        <h1 class="text-2xl font-bold">{{ __('payment.subscribe_title') }}</h1>
        <p class="text-muted text-sm mt-2">{{ __('payment.subscribe_sub') }}</p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">

        {{-- Free --}}
        <div class="card p-6 text-center">
            <h2 class="font-bold mb-1">{{ __('payment.plan_free') }}</h2>
            <div class="text-3xl font-bold my-3">€0</div>
            <ul class="text-xs text-muted space-y-2 mb-6 text-left">
                <li class="flex gap-2"><span class="text-green-400">✓</span> {{ __('payment.free_feature_1') }}</li>
                <li class="flex gap-2"><span class="text-green-400">✓</span> {{ __('payment.free_feature_2') }}</li>
                <li class="flex gap-2"><span class="text-green-400">✓</span> {{ __('payment.free_feature_3') }}</li>
                <li class="flex gap-2"><span class="text-muted">·</span> {{ __('payment.free_limit_1') }}</li>
                <li class="flex gap-2"><span class="text-muted">·</span> {{ __('payment.free_limit_2') }}</li>
            </ul>
            <span class="badge badge-muted">{{ __('payment.current_plan') }}</span>
        </div>

        {{-- Monthly --}}
        <div class="card p-6 text-center border-amber/50 relative">
            <div class="absolute -top-3 left-1/2 -translate-x-1/2 badge badge-amber text-xs">{{ __('payment.popular') }}</div>
            <h2 class="font-bold mb-1">{{ __('payment.plan_monthly') }}</h2>
            <div class="text-3xl font-bold my-3 text-amber">€14.99<span class="text-sm font-normal text-muted">/mo</span></div>
            <ul class="text-xs text-muted space-y-2 mb-6 text-left">
                <li class="flex gap-2"><span class="text-green-400">✓</span> {{ __('payment.monthly_feature_1') }}</li>
                <li class="flex gap-2"><span class="text-green-400">✓</span> {{ __('payment.monthly_feature_2') }}</li>
                <li class="flex gap-2"><span class="text-green-400">✓</span> {{ __('payment.monthly_feature_3') }}</li>
                <li class="flex gap-2"><span class="text-green-400">✓</span> {{ __('payment.monthly_feature_4') }}</li>
            </ul>
            <form method="POST" action="{{ route('payment.subscribe.stripe') }}">
                @csrf <input type="hidden" name="plan" value="monthly">
                <button class="btn-amber w-full text-sm">{{ __('payment.subscribe_btn') }}</button>
            </form>
        </div>

        {{-- Yearly --}}
        <div class="card p-6 text-center border-primary/50">
            <h2 class="font-bold mb-1">{{ __('payment.plan_yearly') }}</h2>
            <div class="text-3xl font-bold my-3 text-primary">€99<span class="text-sm font-normal text-muted">/yr</span></div>
            <ul class="text-xs text-muted space-y-2 mb-6 text-left">
                <li class="flex gap-2"><span class="text-green-400">✓</span> {{ __('payment.yearly_feature_1') }}</li>
                <li class="flex gap-2"><span class="text-green-400">✓</span> {{ __('payment.yearly_feature_2') }}</li>
                <li class="flex gap-2"><span class="text-green-400">✓</span> {{ __('payment.yearly_feature_3') }}</li>
                <li class="flex gap-2"><span class="text-green-400">✓</span> {{ __('payment.yearly_feature_4') }}</li>
            </ul>
            <form method="POST" action="{{ route('payment.subscribe.stripe') }}">
                @csrf <input type="hidden" name="plan" value="yearly">
                <button class="btn-primary w-full text-sm">{{ __('payment.subscribe_btn') }}</button>
            </form>
        </div>
    </div>

    <p class="text-center text-xs text-muted mt-6">{{ __('payment.secure_note') }}</p>
</div>
@endsection
