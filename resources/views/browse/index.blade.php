@extends('layouts.app')
@section('title', __('browse.title'))
@section('content')
<div x-data="{ filtersOpen: false }">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-4">
        <h1 class="text-xl font-bold">{{ __('browse.discover') }}</h1>
        <button @click="filtersOpen=!filtersOpen" class="btn-ghost text-sm flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/></svg>
            {{ __('browse.filters') }}
        </button>
    </div>

    {{-- Filters --}}
    <div x-show="filtersOpen" x-transition class="card p-4 mb-6">
        <form method="GET" action="{{ route('browse.index') }}" class="flex flex-wrap gap-3 items-end">
            <div>
                <label class="text-xs text-muted mb-1 block">{{ __('browse.age_group') }}</label>
                <select name="age_group" class="input text-sm" style="width:auto">
                    <option value="any">{{ __('browse.any_age') }}</option>
                    @foreach(['18-25','25-35','35-45','45-55','55+'] as $ag)
                    <option value="{{ $ag }}" {{ request('age_group')===$ag ? 'selected' : '' }}>{{ $ag }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="text-xs text-muted mb-1 block">{{ __('browse.practice') }}</label>
                <select name="practice" class="input text-sm" style="width:auto">
                    <option value="">{{ __('browse.any_practice') }}</option>
                    @foreach($practices as $p)
                    <option value="{{ $p->id }}" {{ request('practice')==$p->id ? 'selected' : '' }}>{{ $p->label(app()->getLocale()) }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn-primary text-sm">{{ __('browse.apply') }}</button>
            <a href="{{ route('browse.index') }}" class="btn-ghost text-sm">{{ __('browse.reset') }}</a>
        </form>
    </div>

    {{-- Profile grid --}}
    @if($profiles->isEmpty())
    <div class="card p-12 text-center">
        <p class="text-muted text-sm">{{ __('browse.no_profiles') }}</p>
    </div>
    @else
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($profiles as $profile)
        <a href="{{ route('profile.show', $profile->username) }}" class="card hover:border-primary/50 transition-all duration-200 block overflow-hidden group">
            {{-- Blurred photo / placeholder --}}
            <div class="relative h-48 bg-surface flex items-center justify-center overflow-hidden">
                @if($profile->photos->count() > 0)
                <img src="{{ $profile->primaryPhoto?->url ?? $profile->photos->first()->url }}"
                     alt="" class="w-full h-full object-cover blur-xl scale-110 opacity-60">
                @endif
                <div class="absolute inset-0 flex items-center justify-center">
                    <div class="w-16 h-16 rounded-full bg-primary/20 border-2 border-primary/40 flex items-center justify-center text-2xl font-bold text-primary">
                        {{ strtoupper(substr($profile->username, 0, 1)) }}
                    </div>
                </div>
                <div class="absolute top-2 right-2 badge badge-muted text-xs">{{ $profile->age }}</div>
                @if(!$profile->is_incognito)
                <div class="absolute bottom-2 left-2 w-2 h-2 rounded-full bg-green-400 shadow"></div>
                @endif
            </div>

            {{-- Info --}}
            <div class="p-4">
                <div class="flex items-start justify-between mb-2">
                    <h3 class="font-semibold text-sm group-hover:text-primary transition-colors">{{ $profile->username }}</h3>
                    <span class="text-xs text-muted">{{ $profile->location_city }}</span>
                </div>
                <p class="text-xs text-muted line-clamp-3 leading-relaxed">{{ Str::limit($profile->bio, 120) }}</p>
                <div class="mt-3 flex flex-wrap gap-1">
                    @foreach($profile->practices->take(3) as $p)
                    <span class="badge badge-muted">{{ $p->label(app()->getLocale()) }}</span>
                    @endforeach
                    @if($profile->practices->count() > 3)
                    <span class="badge badge-muted">+{{ $profile->practices->count() - 3 }}</span>
                    @endif
                </div>
            </div>
        </a>
        @endforeach
    </div>

    {{-- Pagination --}}
    <div class="mt-8">{{ $profiles->withQueryString()->links() }}</div>
    @endif
</div>
@endsection
