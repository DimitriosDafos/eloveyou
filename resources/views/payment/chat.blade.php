@extends('layouts.app')
@section('title', __('payment.unlock_chat'))
@section('content')
<div class="max-w-md mx-auto">
    <div class="card p-8 text-center">
        <div class="w-16 h-16 rounded-full bg-primary/10 border border-primary/30 flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
            </svg>
        </div>

        <h1 class="text-xl font-bold mb-2">{{ __('payment.unlock_chat') }}</h1>
        <p class="text-muted text-sm mb-6">{{ __('payment.unlock_sub') }}</p>

        {{-- Price display --}}
        <div class="card p-4 mb-6">
            <div class="text-3xl font-bold text-primary mb-1">€{{ number_format($amount, 2) }}</div>
            <p class="text-sm text-muted">{{ __('payment.for_24h') }}</p>
            @if($isSubscribed)
            <p class="text-xs text-amber mt-1">{{ __('payment.subscriber_rate') }}</p>
            @endif
        </div>

        {{-- What you get --}}
        <ul class="text-sm text-left space-y-2 mb-6">
            <li class="flex items-center gap-2 text-muted"><span class="text-green-400">✓</span> {{ __('payment.get_24h_chat') }}</li>
            <li class="flex items-center gap-2 text-muted"><span class="text-green-400">✓</span> {{ __('payment.get_photos') }}</li>
            <li class="flex items-center gap-2 text-muted"><span class="text-green-400">✓</span> {{ __('payment.get_emojis') }}</li>
        </ul>

        {{-- Stripe --}}
        <form method="POST" action="{{ route('payment.chat.stripe', $chat->id) }}" class="mb-3">
            @csrf
            <button type="submit" class="btn-primary w-full flex items-center justify-center gap-2">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M13.976 9.15c-2.172-.806-3.356-1.426-3.356-2.409 0-.831.683-1.305 1.901-1.305 2.227 0 4.515.858 6.09 1.631l.89-5.494C18.252.975 15.697 0 12.165 0 9.667 0 7.589.654 6.104 1.872 4.56 3.147 3.757 4.992 3.757 7.218c0 4.039 2.467 5.76 6.476 7.219 2.585.92 3.445 1.574 3.445 2.583 0 .98-.84 1.545-2.354 1.545-1.875 0-4.965-.921-6.99-2.109l-.9 5.555C5.175 22.99 8.385 24 11.714 24c2.641 0 4.843-.624 6.328-1.813 1.664-1.305 2.525-3.236 2.525-5.732 0-4.128-2.524-5.851-6.591-7.305z"/></svg>
                {{ __('payment.pay_stripe') }}
            </button>
        </form>

        {{-- PayPal --}}
        <form method="POST" action="{{ route('payment.chat.paypal', $chat->id) }}">
            @csrf
            <button type="submit" class="btn-ghost w-full flex items-center justify-center gap-2 text-[#003087]" style="border-color:#003087">
                <span class="font-bold text-[#003087]">Pay</span><span class="font-bold text-[#009cde]">Pal</span>
            </button>
        </form>

        {{-- Subscribe upsell --}}
        @if(!$isSubscribed)
        <div class="mt-6 pt-6 border-t border-border">
            <p class="text-xs text-muted mb-2">{{ __('payment.subscribe_save') }}</p>
            <a href="{{ route('payment.subscribe') }}" class="text-amber text-sm hover:underline">{{ __('payment.see_plans') }} →</a>
        </div>
        @endif
    </div>
</div>
@endsection
