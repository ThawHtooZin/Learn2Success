<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="theme-color" content="#785900">
    <title>@yield('title', config('app.name'))</title>
    @fonts
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
    @stack('scripts')
</head>
<body class="bg-[#f6fafe] antialiased">
    <div class="student-app relative pb-[5.5rem]">
        <header class="sticky top-0 z-30 flex h-16 items-center justify-between border-b-2 border-[#cde5ff] bg-white/90 px-5 backdrop-blur-md">
            <img
                src="{{ asset(config('branding.logo')) }}"
                alt="{{ config('app.name') }}"
                class="h-10 w-auto max-w-[9.5rem] object-contain object-left"
            >
            <span class="rounded-full bg-[#ffdf9e] px-4 py-1.5 text-base font-bold text-[#6d5100]">
                {{ auth()->user()->username }}
            </span>
        </header>

        <main class="page-enter px-5 py-5">
            <x-flash-messages />
            @yield('content')
        </main>

        <nav class="fixed inset-x-0 bottom-0 z-40 w-full border-t-2 border-[#cde5ff] bg-white/95 pb-[env(safe-area-inset-bottom)] shadow-[0_-8px_30px_rgb(0_168_255_/_0.12)] backdrop-blur-md">
            <div class="grid grid-cols-2">
                <a
                    href="{{ route('student.dashboard') }}"
                    class="flex min-h-[4.5rem] flex-col items-center justify-center gap-1 {{ request()->routeIs('student.dashboard') ? 'text-[#785900]' : 'text-[#006399]' }}"
                >
                    <span class="text-2xl" aria-hidden="true">🏠</span>
                    <span class="text-base font-bold">Home</span>
                </a>
                <form method="POST" action="{{ route('logout') }}" class="contents">
                    @csrf
                    <button type="submit" class="student-btn-logout">
                        <span class="text-2xl" aria-hidden="true">👋</span>
                        <span class="text-base font-bold">Log out</span>
                    </button>
                </form>
            </div>
        </nav>
    </div>
</body>
</html>
