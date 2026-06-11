<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name'))</title>
    @fonts
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
    @stack('scripts')
</head>
<body class="min-h-screen bg-slate-100 text-slate-900 antialiased" x-data="{ sidebarOpen: false }">
    <div class="flex min-h-screen">
        <div
            class="fixed inset-0 z-40 bg-black/40 lg:hidden"
            x-show="sidebarOpen"
            x-cloak
            @click="sidebarOpen = false"
        ></div>

        <aside
            class="fixed inset-y-0 left-0 z-50 w-64 -translate-x-full border-r border-slate-200 bg-white transition-transform lg:static lg:translate-x-0"
            :class="{ 'translate-x-0': sidebarOpen }"
        >
            <div class="flex h-16 items-center gap-3 border-b border-[#cde5ff] px-4">
                <img src="{{ asset(config('branding.logo')) }}" alt="" class="h-9 w-9 object-contain">
                <span class="font-bold text-[#785900]">{{ config('app.name') }}</span>
            </div>
            <nav class="space-y-1 p-4 text-sm">
                @php
                    $navClass = fn (string $pattern) => request()->routeIs($pattern)
                        ? 'bg-[#785900] font-semibold text-white hover:bg-[#6d5100]'
                        : 'font-medium text-[#171c1f] hover:bg-[#e3f2fd] hover:text-[#006399]';
                @endphp
                @if (auth()->user()->role === \App\Enums\UserRole::Teacher)
                    <a href="{{ route('teacher.dashboard') }}" class="block rounded-lg px-3 py-2.5 {{ $navClass('teacher.dashboard') }}">Dashboard</a>
                    <a href="{{ route('teacher.submissions.index') }}" class="block rounded-lg px-3 py-2.5 {{ $navClass('teacher.submissions.*') }}">Grade</a>
                    <a href="{{ route('teacher.submissions.by-student') }}" class="block rounded-lg px-3 py-2.5 {{ $navClass('teacher.submissions.by-student') }}">Students</a>
                @endif
                @if (auth()->user()->role === \App\Enums\UserRole::Admin)
                    <a href="{{ route('admin.dashboard') }}" class="block rounded-lg px-3 py-2.5 {{ $navClass('admin.dashboard') }}">Dashboard</a>
                    <a href="{{ route('admin.weeks.index') }}" class="block rounded-lg px-3 py-2.5 {{ $navClass('admin.weeks.*') }}">Weeks</a>
                    <a href="{{ route('manage.quizzes.index') }}" class="block rounded-lg px-3 py-2.5 {{ $navClass('manage.quizzes.*') }}">Quizzes</a>
                    <a href="{{ route('admin.users.index') }}" class="block rounded-lg px-3 py-2.5 {{ $navClass('admin.users.*') }}">Users</a>
                @endif
            </nav>
        </aside>

        <div class="flex min-w-0 flex-1 flex-col">
            <header class="sticky top-0 z-30 flex h-16 items-center justify-between border-b border-slate-200 bg-white px-4">
                <button type="button" class="rounded-lg p-2 hover:bg-slate-100 lg:hidden" @click="sidebarOpen = true" aria-label="Open menu">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <div class="text-sm font-medium">{{ auth()->user()->username }}</div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="min-h-11 rounded-lg px-3 py-2 text-sm font-medium hover:bg-slate-100">Log out</button>
                </form>
            </header>

            <main class="flex-1 p-4 md:p-6">
                <x-flash-messages />
                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>
