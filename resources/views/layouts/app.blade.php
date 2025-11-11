<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Favicon -->
    @php
        $favicon = \App\Models\Setting::get('site_favicon');
    @endphp
    @if($favicon)
        <link rel="icon" type="image/png" href="{{ asset('storage/' . $favicon) }}">
    @endif

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased">
    <div class="h-screen">
        <!-- Sidebar -->
        <x-sidebar />
        <!-- Main content -->
        <div class="pl-64 flex flex-col h-full">
            <main class="flex-1 overflow-y-auto px-4 py-8 sm:px-6 lg:px-8 bg-white min-h-screen">
                {{ $slot }}
            </main>
        </div>
    </div>
</body>
</html>
