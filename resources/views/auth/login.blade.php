@extends('layouts.guest')

@section('title', 'Sign in — '.config('app.name'))

@section('content')
    <main class="flex min-h-screen items-center justify-center bg-gradient-to-b from-[#E3F2FD] to-[#f6fafe] px-4 py-12">
        <div class="w-full max-w-md rounded-2xl border-2 border-[#cde5ff] bg-white p-6 shadow-lg">
            <div class="text-center">
                <img
                    src="{{ asset(config('branding.logo')) }}"
                    alt="{{ config('app.name') }}"
                    class="mx-auto h-auto w-44 max-w-full object-contain"
                >
                <h1 class="mt-3 text-2xl font-bold text-[#785900]">Sign in</h1>
                <p class="mt-1 text-base font-medium text-[#4f4632]">Use your username and password.</p>
            </div>

            <form method="POST" action="{{ route('login') }}" class="mt-6 space-y-4">
                @csrf

                <div>
                    <label for="username" class="block text-base font-bold text-[#785900]">Username</label>
                    <input
                        id="username"
                        name="username"
                        type="text"
                        value="{{ old('username') }}"
                        autocomplete="username"
                        required
                        autofocus
                        class="mt-1 block w-full rounded-xl border-2 border-[#cde5ff] px-4 py-3 text-base focus:border-[#006399] focus:outline-none focus:ring-2 focus:ring-[#04a8ff]/30"
                    >
                    @error('username')
                        <p class="mt-1 text-sm font-semibold text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-base font-bold text-[#785900]">Password</label>
                    <input
                        id="password"
                        name="password"
                        type="password"
                        autocomplete="current-password"
                        required
                        class="mt-1 block w-full rounded-xl border-2 border-[#cde5ff] px-4 py-3 text-base focus:border-[#006399] focus:outline-none focus:ring-2 focus:ring-[#04a8ff]/30"
                    >
                    @error('password')
                        <p class="mt-1 text-sm font-semibold text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <label class="flex items-center gap-2 text-base font-medium text-[#4f4632]">
                    <input
                        type="checkbox"
                        name="remember"
                        value="1"
                        @checked(old('remember'))
                        class="rounded border-[#cde5ff] text-[#785900] focus:ring-[#ffc107]"
                    >
                    Remember me
                </label>

                <button type="submit" class="guest-btn-primary">
                    Sign in
                </button>
            </form>

            <p class="mt-4 text-center text-base font-medium text-[#006399]">
                <a href="{{ route('home') }}" class="font-bold hover:underline">Back to home</a>
            </p>
        </div>
    </main>
@endsection
