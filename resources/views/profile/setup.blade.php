@extends('layouts.app')
@section('title', __('profile.setup_title'))
@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-6">
        <h1 class="text-2xl font-bold">{{ __('profile.setup_title') }}</h1>
        <p class="text-muted text-sm mt-1">{{ __('profile.setup_sub') }}</p>
    </div>

    @if(count($missing) > 0)
    <div class="card border-amber/30 px-4 py-3 mb-6 text-sm text-amber">
        {{ __('profile.complete_to_browse') }}
    </div>
    @endif

    <form method="POST" action="{{ route('profile.update') }}" class="space-y-6" x-data>
        @csrf

        {{-- Age & Gender --}}
        <div class="card p-6 space-y-4">
            <h2 class="font-semibold text-sm text-muted uppercase tracking-wider">{{ __('profile.basics') }}</h2>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label>{{ __('profile.age') }} <span class="text-primary">*</span></label>
                    <input class="input" type="number" name="age" value="{{ old('age', $user->age) }}" min="18" max="120" required>
                </div>
                <div>
                    <label>{{ __('profile.gender') }} <span class="text-primary">*</span></label>
                    <select class="input" name="gender" required>
                        <option value="">—</option>
                        @foreach(['male','female','non_binary','trans_male','trans_female','other'] as $g)
                        <option value="{{ $g }}" {{ old('gender', $user->gender) === $g ? 'selected' : '' }}>{{ __('profile.gender_'.$g) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label>{{ __('profile.city') }} <span class="text-primary">*</span></label>
                    <input class="input" type="text" name="location_city" value="{{ old('location_city', $user->location_city) }}" placeholder="{{ __('profile.city_placeholder') }}" required>
                </div>
                <div>
                    <label>{{ __('profile.region') }} <span class="text-primary">*</span></label>
                    <input class="input" type="text" name="location_region" value="{{ old('location_region', $user->location_region) }}" placeholder="{{ __('profile.region_placeholder') }}" required>
                </div>
            </div>
        </div>

        {{-- Looking for --}}
        <div class="card p-6 space-y-3">
            <h2 class="font-semibold text-sm text-muted uppercase tracking-wider">{{ __('profile.looking_for') }} <span class="text-primary">*</span></h2>
            <p class="text-xs text-muted">{{ __('profile.looking_for_hint') }}</p>
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                @foreach(['men','women','non_binary','trans','couples','groups','any'] as $opt)
                <label class="flex items-center gap-2 cursor-pointer card px-3 py-2 hover:border-primary/50 transition-colors">
                    <input type="checkbox" name="looking_for[]" value="{{ $opt }}" class="accent-primary"
                           {{ in_array($opt, old('looking_for', $user->looking_for ?? [])) ? 'checked' : '' }}>
                    <span class="text-sm">{{ __('profile.looking_'.$opt) }}</span>
                </label>
                @endforeach
            </div>
        </div>

        {{-- Sexual Practices --}}
        <div class="card p-6 space-y-3">
            <h2 class="font-semibold text-sm text-muted uppercase tracking-wider">{{ __('profile.practices') }} <span class="text-primary">*</span></h2>
            <p class="text-xs text-muted">{{ __('profile.practices_hint') }}</p>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                @foreach($practices as $practice)
                <label class="flex items-center gap-2 cursor-pointer card px-3 py-2 hover:border-primary/50 transition-colors">
                    <input type="checkbox" name="practices[]" value="{{ $practice->id }}" class="accent-primary"
                           {{ in_array($practice->id, old('practices', $user->practices->pluck('id')->toArray())) ? 'checked' : '' }}>
                    <span class="text-sm">{{ $practice->label(app()->getLocale()) }}</span>
                </label>
                @endforeach
            </div>
        </div>

        {{-- Bio --}}
        <div class="card p-6 space-y-3">
            <h2 class="font-semibold text-sm text-muted uppercase tracking-wider">{{ __('profile.bio') }} <span class="text-primary">*</span></h2>
            <p class="text-xs text-muted">{{ __('profile.bio_hint') }}</p>
            <div x-data="{ count: {{ strlen(old('bio', $user->bio ?? '')) }} }">
                <textarea class="input" name="bio" rows="8" maxlength="3000" required
                          placeholder="{{ __('profile.bio_placeholder') }}"
                          x-on:input="count=$el.value.length"
                          >{{ old('bio', $user->bio) }}</textarea>
                <div class="flex justify-between text-xs text-muted mt-1">
                    <span>{{ __('profile.bio_min') }}</span>
                    <span x-text="count + '/3000'"></span>
                </div>
            </div>
        </div>

        <div class="flex gap-3">
            <button type="submit" class="btn-primary">{{ __('profile.save') }}</button>
            @if($user->profile_complete)
            <a href="{{ route('browse.index') }}" class="btn-ghost">{{ __('profile.skip') }}</a>
            @endif
        </div>
    </form>
</div>
@endsection
