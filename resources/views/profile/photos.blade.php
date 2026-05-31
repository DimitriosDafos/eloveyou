@extends('layouts.app')
@section('title', __('profile.photos_title'))
@section('content')
<div class="max-w-2xl mx-auto">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold">{{ __('profile.photos_title') }}</h1>
            <p class="text-muted text-sm mt-1">{{ __('profile.photos_sub') }}</p>
        </div>
        <a href="{{ route('browse.index') }}" class="btn-primary text-sm">{{ __('profile.go_browse') }}</a>
    </div>

    {{-- Upload rules --}}
    <div class="card border-amber/30 px-4 py-4 mb-6">
        <p class="text-sm font-semibold text-amber mb-2">{{ __('profile.photo_rules_title') }}</p>
        <ul class="text-xs text-muted space-y-1 list-disc list-inside">
            <li>{{ __('profile.photo_rule_1') }}</li>
            <li>{{ __('profile.photo_rule_2') }}</li>
            <li>{{ __('profile.photo_rule_3') }}</li>
            <li>{{ __('profile.photo_rule_4') }}</li>
        </ul>
    </div>

    {{-- Current photos --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 mb-6">
        @forelse($user->photos as $photo)
        <div class="card overflow-hidden relative group">
            <img src="{{ $photo->url }}" alt="" class="w-full h-40 object-cover">
            <div class="p-2 flex items-center justify-between">
                <span class="badge {{ $photo->status === 'approved' ? 'badge-green' : ($photo->status === 'removed' ? 'badge-red' : 'badge-amber') }}">
                    {{ __('profile.photo_status_'.$photo->status) }}
                </span>
                <form method="POST" action="{{ route('profile.photos.delete', $photo->id) }}">
                    @csrf @method('DELETE')
                    <button class="text-xs text-muted hover:text-primary transition-colors">{{ __('ui.delete') }}</button>
                </form>
            </div>
        </div>
        @empty
        <div class="col-span-3 card p-8 text-center text-muted text-sm">
            {{ __('profile.no_photos_yet') }}
        </div>
        @endforelse
    </div>

    {{-- Upload new photo --}}
    @if($user->photos->count() < 5)
    <div class="card p-6" x-data="{ dragging: false }">
        <h2 class="font-semibold mb-4">{{ __('profile.upload_photo') }}</h2>
        <form method="POST" action="{{ route('profile.photos.upload') }}" enctype="multipart/form-data">
            @csrf
            <div class="border-2 border-dashed border-border rounded-lg p-8 text-center transition-colors"
                 :class="dragging ? 'border-primary bg-primary/5' : ''"
                 @dragover.prevent="dragging=true" @dragleave="dragging=false" @drop.prevent="dragging=false">
                <input type="file" name="photo" id="photo" accept="image/jpeg,image/png,image/webp" class="hidden"
                       @change="$el.closest('form').querySelector('span').textContent = $el.files[0]?.name ?? ''">
                <label for="photo" class="cursor-pointer">
                    <svg class="w-10 h-10 text-muted mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <span class="text-sm text-muted">{{ __('profile.drag_or_click') }}</span>
                    <p class="text-xs text-muted mt-1">{{ __('profile.photo_formats') }}</p>
                </label>
            </div>
            <p class="text-xs text-muted mt-1" x-data><span></span></p>
            <button type="submit" class="btn-primary mt-4">{{ __('profile.upload_btn') }}</button>
        </form>
    </div>
    @else
    <div class="card p-4 text-center text-sm text-muted">{{ __('profile.max_photos_reached') }}</div>
    @endif

    {{-- Incognito toggle --}}
    <div class="card p-4 flex items-center justify-between mt-6">
        <div>
            <p class="font-medium text-sm">{{ __('profile.incognito_mode') }}</p>
            <p class="text-xs text-muted mt-0.5">{{ __('profile.incognito_hint') }}</p>
        </div>
        <form method="POST" action="{{ route('profile.incognito') }}">
            @csrf
            <button type="submit" class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors
                {{ $user->is_incognito ? 'bg-primary' : 'bg-border' }}">
                <span class="inline-block h-4 w-4 rounded-full bg-white transform transition-transform
                    {{ $user->is_incognito ? 'translate-x-6' : 'translate-x-1' }}"></span>
            </button>
        </form>
    </div>

    {{-- Delete account --}}
    <div class="card p-4 border-red-900/50 mt-4" x-data="{ open: false }">
        <button @click="open=!open" class="text-sm text-muted hover:text-red-400 transition-colors">{{ __('profile.delete_account') }}</button>
        <div x-show="open" x-transition class="mt-4">
            <p class="text-xs text-muted mb-3">{{ __('profile.delete_warning') }}</p>
            <form method="POST" action="{{ route('profile.delete') }}">
                @csrf @method('DELETE')
                <input class="input mb-3" type="password" name="password" placeholder="{{ __('auth.password') }}" required>
                <button type="submit" class="text-sm text-red-400 hover:text-red-300 font-medium">{{ __('profile.confirm_delete') }}</button>
            </form>
        </div>
    </div>
</div>
@endsection
