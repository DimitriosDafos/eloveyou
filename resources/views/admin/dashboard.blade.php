@extends('layouts.app')
@section('title', 'Admin Dashboard')
@section('content')
<div>
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-xl font-bold">{{ __('admin.dashboard') }}</h1>
        <span class="badge badge-red">Admin</span>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 sm:grid-cols-5 gap-4 mb-8">
        @foreach([
            ['label' => __('admin.total_users'), 'value' => $totalUsers, 'color' => 'text-text'],
            ['label' => __('admin.active_today'), 'value' => $activeToday, 'color' => 'text-green-400'],
            ['label' => __('admin.pending_reports'), 'value' => $pendingReports, 'color' => 'text-red-400'],
            ['label' => __('admin.pending_photos'), 'value' => $pendingPhotos, 'color' => 'text-amber'],
            ['label' => __('admin.revenue_30d'), 'value' => '€'.number_format($revenue30d,2), 'color' => 'text-amber'],
        ] as $stat)
        <div class="card p-4 text-center">
            <div class="text-2xl font-bold {{ $stat['color'] }}">{{ $stat['value'] }}</div>
            <div class="text-xs text-muted mt-1">{{ $stat['label'] }}</div>
        </div>
        @endforeach
    </div>

    {{-- Quick links --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
        @foreach([
            ['route' => 'admin.users', 'label' => __('admin.users'), 'icon' => '👥'],
            ['route' => 'admin.reports', 'label' => __('admin.reports'), 'icon' => '🚩'],
            ['route' => 'admin.photos', 'label' => __('admin.photos'), 'icon' => '🖼'],
            ['route' => 'admin.payments', 'label' => __('admin.payments'), 'icon' => '💳'],
            ['route' => 'admin.stats', 'label' => __('admin.stats'), 'icon' => '📊'],
            ['route' => 'admin.admins', 'label' => __('admin.admins'), 'icon' => '🔑'],
        ] as $link)
        <a href="{{ route($link['route']) }}" class="card p-4 hover:border-primary/50 transition-colors flex items-center gap-3">
            <span class="text-xl">{{ $link['icon'] }}</span>
            <span class="font-medium text-sm">{{ $link['label'] }}</span>
        </a>
        @endforeach
    </div>
</div>
@endsection
