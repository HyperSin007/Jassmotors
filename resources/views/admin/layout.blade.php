<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin Panel') - {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 font-sans">
    <div class="min-h-screen flex">
        <aside class="w-64 bg-white border-r">
            <div class="p-4 font-bold text-lg">{{ config('app.name') }} Admin</div>
            <nav class="p-4">
                <ul>
                    <li class="mb-2"><a href="{{ route('admin.dashboard') }}" class="text-gray-700 hover:text-gray-900">Dashboard</a></li>
                    <li class="mb-2"><a href="{{ route('admin.invoices') }}" class="text-gray-700 hover:text-gray-900">Invoices</a></li>
                    <li><a href="{{ route('admin.users.index') }}" class="text-gray-700 hover:text-gray-900">Users</a></li>
                </ul>
            </nav>
        </aside>

        <main class="flex-1 p-6">
            <header class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-semibold">@yield('heading')</h1>
                <div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-sm text-red-600">Logout</button>
                    </form>
                </div>
            </header>

            <div class="bg-white shadow rounded p-4">
                @yield('content')
            </div>
        </main>
    </div>
</body>
</html>
