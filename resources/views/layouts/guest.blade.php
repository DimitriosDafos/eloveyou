<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#0d0b14">
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <title>{{ config('app.name') }} — @yield('title', __('ui.tagline'))</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: { bg:'#0d0b14', surface:'#150f20', border:'#2a1f35', primary:'#c0284a', amber:'#e8a045', text:'#f0edf5', muted:'#6b6278' },
                    fontFamily: { sans: ['DM Sans','sans-serif'] },
                }
            }
        }
    </script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        body { background-color: #0d0b14; color: #f0edf5; font-family: 'DM Sans', sans-serif; }
        .btn-primary { background:#c0284a; color:#f0edf5; padding:.6rem 1.4rem; border-radius:.5rem; font-weight:600; transition:background .2s; display:inline-block; }
        .btn-primary:hover { background:#a82040; }
        .btn-ghost { border:1px solid #2a1f35; color:#f0edf5; padding:.6rem 1.4rem; border-radius:.5rem; font-weight:500; transition:all .2s; display:inline-block; }
        .btn-ghost:hover { border-color:#c0284a; color:#c0284a; }
        .card { background:#150f20; border:1px solid #2a1f35; border-radius:.75rem; }
        .input { background:#0d0b14; border:1px solid #2a1f35; color:#f0edf5; border-radius:.5rem; padding:.65rem .9rem; width:100%; transition:border .2s; }
        .input:focus { outline:none; border-color:#c0284a; }
        .input::placeholder { color:#6b6278; }
        label { display:block; font-size:.875rem; color:#6b6278; margin-bottom:.35rem; }
    </style>
</head>
<body class="min-h-screen flex flex-col">

    <div class="flex-1 flex flex-col items-center justify-center px-4 py-12">
        <a href="/" class="mb-8 text-2xl font-bold"><span class="text-primary">e</span>loveyou</a>
        <div class="w-full max-w-md">
            @if($errors->any())
            <div class="card border-red-800 px-4 py-3 text-sm text-red-300 mb-4">
                <ul class="list-disc list-inside space-y-1">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
            @endif
            @if(session('success'))
            <div class="card border-green-800 px-4 py-3 text-sm text-green-300 mb-4">{{ session('success') }}</div>
            @endif
            @yield('content')
        </div>
        <div class="mt-6 flex gap-4 text-xs text-muted">
            <form method="POST" action="{{ route('locale.switch', 'en') }}">@csrf<button class="{{ app()->getLocale()==='en' ? 'text-primary' : '' }}">EN</button></form>
            <span>|</span>
            <form method="POST" action="{{ route('locale.switch', 'de') }}">@csrf<button class="{{ app()->getLocale()==='de' ? 'text-primary' : '' }}">DE</button></form>
        </div>
    </div>

</body>
</html>
