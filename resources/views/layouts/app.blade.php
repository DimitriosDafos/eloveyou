<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#0d0b14">

    {{-- PWA --}}
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <link rel="apple-touch-icon" href="{{ asset('icons/icon-192.png') }}">

    <title>{{ config('app.name') }} — @yield('title', __('ui.tagline'))</title>

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    {{-- Tailwind CDN --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        bg:      '#0d0b14',
                        surface: '#150f20',
                        border:  '#2a1f35',
                        primary: '#c0284a',
                        amber:   '#e8a045',
                        text:    '#f0edf5',
                        muted:   '#6b6278',
                    },
                    fontFamily: { sans: ['DM Sans', 'sans-serif'] },
                }
            }
        }
    </script>

    {{-- Alpine.js --}}
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    {{-- Laravel Echo + Pusher (Reverb) --}}
    @auth
    <script src="https://unpkg.com/pusher-js@8.4.0/dist/web/pusher.min.js"></script>
    <script src="https://unpkg.com/laravel-echo@1.16.0/dist/echo.iife.js"></script>
    <script>
        window.Echo = new Echo({
            broadcaster: 'reverb',
            key: '{{ config('broadcasting.connections.reverb.key') }}',
            wsHost: '{{ config('broadcasting.connections.reverb.options.host') ?? 'webapps.dafos.eu' }}',
            wsPort: 443,
            wssPort: 443,
            forceTLS: true,
            enabledTransports: ['ws', 'wss'],
        });
    </script>
    @endauth

    <style>
        body { background-color: #0d0b14; color: #f0edf5; font-family: 'DM Sans', sans-serif; }
        ::-webkit-scrollbar { width: 6px; } ::-webkit-scrollbar-track { background: #0d0b14; } ::-webkit-scrollbar-thumb { background: #2a1f35; border-radius: 3px; }
        .btn-primary { background: #c0284a; color: #f0edf5; padding: .6rem 1.4rem; border-radius: .5rem; font-weight: 600; transition: background .2s; display: inline-block; }
        .btn-primary:hover { background: #a82040; }
        .btn-amber { background: #e8a045; color: #0d0b14; padding: .6rem 1.4rem; border-radius: .5rem; font-weight: 700; transition: background .2s; display: inline-block; }
        .btn-amber:hover { background: #d08a30; }
        .btn-ghost { border: 1px solid #2a1f35; color: #f0edf5; padding: .6rem 1.4rem; border-radius: .5rem; font-weight: 500; transition: all .2s; display: inline-block; }
        .btn-ghost:hover { border-color: #c0284a; color: #c0284a; }
        .card { background: #150f20; border: 1px solid #2a1f35; border-radius: .75rem; }
        .input { background: #0d0b14; border: 1px solid #2a1f35; color: #f0edf5; border-radius: .5rem; padding: .65rem .9rem; width: 100%; transition: border .2s; }
        .input:focus { outline: none; border-color: #c0284a; }
        .input::placeholder { color: #6b6278; }
        .badge { font-size:.7rem; padding:.2rem .5rem; border-radius:.3rem; font-weight:600; }
        .badge-red { background:#c0284a22; color:#c0284a; }
        .badge-amber { background:#e8a04522; color:#e8a045; }
        .badge-green { background:#22c55e22; color:#22c55e; }
        .badge-muted { background:#2a1f35; color:#6b6278; }
    </style>
    @stack('styles')
</head>
<body class="min-h-screen">

    {{-- Navigation --}}
    @auth
    <nav class="border-b border-border sticky top-0 z-50" style="background:#0d0b14ee;backdrop-filter:blur(12px)">
        <div class="max-w-5xl mx-auto px-4 flex items-center justify-between h-14">
            <a href="{{ route('browse.index') }}" class="flex items-center gap-2">
                <span class="text-xl font-bold"><span class="text-primary">e</span>loveyou</span>
            </a>
            <div class="flex items-center gap-3">
                <a href="{{ route('browse.index') }}" class="text-muted hover:text-text text-sm transition-colors">{{ __('nav.browse') }}</a>
                <a href="{{ route('matches.index') }}" class="text-muted hover:text-text text-sm transition-colors">{{ __('nav.matches') }}</a>
                <a href="{{ route('chats.index') }}" class="text-muted hover:text-text text-sm transition-colors">{{ __('nav.chats') }}</a>
                @if(auth()->user()->isAdmin())
                <a href="{{ route('admin.dashboard') }}" class="text-amber text-sm font-medium">Admin</a>
                @endif
                <div x-data="{ open: false }" class="relative">
                    <button @click="open=!open" class="w-8 h-8 rounded-full bg-primary flex items-center justify-center text-sm font-bold">
                        {{ strtoupper(substr(auth()->user()->username, 0, 1)) }}
                    </button>
                    <div x-show="open" @click.outside="open=false" x-transition
                         class="absolute right-0 top-10 card min-w-[180px] py-1 shadow-xl z-50">
                        <a href="{{ route('profile.setup') }}" class="block px-4 py-2 text-sm text-text hover:bg-border transition-colors">{{ __('nav.my_profile') }}</a>
                        <a href="{{ route('profile.photos') }}" class="block px-4 py-2 text-sm text-text hover:bg-border transition-colors">{{ __('nav.my_photos') }}</a>
                        <a href="{{ route('payment.subscribe') }}" class="block px-4 py-2 text-sm text-amber hover:bg-border transition-colors">{{ __('nav.subscribe') }}</a>
                        <hr class="border-border my-1">
                        {{-- Language switch --}}
                        <div class="px-4 py-2 flex gap-2">
                            <form method="POST" action="{{ route('locale.save') }}">@csrf<input type="hidden" name="locale" value="en"><button class="text-xs {{ app()->getLocale()==='en' ? 'text-primary font-bold' : 'text-muted' }}">EN</button></form>
                            <span class="text-muted text-xs">|</span>
                            <form method="POST" action="{{ route('locale.save') }}">@csrf<input type="hidden" name="locale" value="de"><button class="text-xs {{ app()->getLocale()==='de' ? 'text-primary font-bold' : 'text-muted' }}">DE</button></form>
                        </div>
                        <hr class="border-border my-1">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="block w-full text-left px-4 py-2 text-sm text-muted hover:text-text transition-colors">{{ __('nav.logout') }}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </nav>
    @endauth

    {{-- Flash messages --}}
    <div class="max-w-5xl mx-auto px-4 pt-4 space-y-2">
        @foreach (['success','warning','info','error'] as $type)
            @if(session($type))
            <div x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(()=>show=false,5000)"
                 class="card px-4 py-3 text-sm flex items-center justify-between
                    {{ $type==='success' ? 'border-green-800 text-green-300' :
                       ($type==='warning' ? 'border-amber-800 text-amber-300' :
                       ($type==='error' ? 'border-red-800 text-red-300' : 'border-border text-muted')) }}">
                <span>{{ session($type) }}</span>
                <button @click="show=false" class="ml-4 opacity-50 hover:opacity-100">✕</button>
            </div>
            @endif
        @endforeach
        @if($errors->any())
        <div class="card border-red-800 px-4 py-3 text-sm text-red-300">
            <ul class="list-disc list-inside space-y-1">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
        @endif
    </div>

    {{-- Page content --}}
    <main class="max-w-5xl mx-auto px-4 py-6">
        @yield('content')
    </main>

    {{-- Cookie consent --}}
    @if(!session('cookie_ok'))
    <div x-data="{ show: true }" x-show="show" x-transition
         class="fixed bottom-0 left-0 right-0 z-50 card border-t border-border rounded-none px-6 py-4">
        <div class="max-w-5xl mx-auto flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <p class="text-sm text-muted">{{ __('ui.cookie_text') }}</p>
            <form method="POST" action="/locale/en" class="hidden"></form>
            <div class="flex gap-3">
                <a href="#" onclick="fetch('/cookie-accept',{method:'POST',headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}'}}).then(()=>document.getElementById('cookie-bar').remove())" @click="show=false" class="btn-primary text-sm">{{ __('ui.accept') }}</a>
            </div>
        </div>
    </div>
    @endif

    @stack('scripts')
</body>
</html>
