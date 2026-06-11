@extends('layouts.guest')

@section('title', config('app.name'))

@section('content')
    <main class="min-h-screen w-full bg-gradient-to-b from-[#E3F2FD] to-[#f6fafe]">
        {{-- Hero + banner --}}
        <section class="mx-auto w-full max-w-6xl px-4 pb-8 pt-6 sm:px-6 sm:pt-10 lg:pb-12">
            <div class="lg:grid lg:grid-cols-12 lg:items-center lg:gap-10 xl:gap-14">
                <div class="text-center lg:col-span-5 lg:text-left">
                    <img
                        src="{{ asset(config('branding.logo')) }}"
                        alt="{{ config('app.name') }}"
                        class="mx-auto h-auto w-48 max-w-full object-contain drop-shadow-md sm:w-56 lg:mx-0 lg:w-64"
                    >
                    <p class="mt-5 text-lg font-semibold leading-relaxed text-[#171c1f] sm:text-xl lg:max-w-md">
                        {{ config('branding.tagline') }}
                    </p>

                    <div class="mt-8 hidden lg:block">
                        <a href="{{ route('login') }}" class="guest-btn-primary !w-auto !min-w-[14rem] !px-8">
                            Sign in to start
                        </a>
                    </div>
                </div>

                <div class="mt-8 lg:col-span-7 lg:mt-0">
                    <x-image-carousel class="w-full" />
                </div>
            </div>

            <div class="mx-auto mt-8 max-w-md lg:hidden">
                <a href="{{ route('login') }}" class="guest-btn-primary">
                    Sign in to start
                </a>
            </div>
        </section>

        {{-- Feature chips --}}
        <section class="mx-auto w-full max-w-6xl px-4 pb-12 sm:px-6">
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 lg:gap-6">
                <div class="student-card text-center">
                    <div class="text-4xl sm:text-5xl">🎧</div>
                    <h2 class="mt-3 text-lg font-bold text-[#785900] sm:text-xl">Listen</h2>
                    <p class="mt-2 text-sm font-medium text-[#4f4632] sm:text-base">Hear questions with a big friendly Listen button.</p>
                </div>
                <div class="student-card text-center">
                    <div class="text-4xl sm:text-5xl">🎤</div>
                    <h2 class="mt-3 text-lg font-bold text-[#785900] sm:text-xl">Speak</h2>
                    <p class="mt-2 text-sm font-medium text-[#4f4632] sm:text-base">Record your voice right in the browser.</p>
                </div>
                <div class="student-card text-center sm:col-span-2 lg:col-span-1">
                    <div class="text-4xl sm:text-5xl">🏆</div>
                    <h2 class="mt-3 text-lg font-bold text-[#785900] sm:text-xl">Succeed</h2>
                    <p class="mt-2 text-sm font-medium text-[#4f4632] sm:text-base">Earn marks and celebrate your progress.</p>
                </div>
            </div>
        </section>
    </main>
@endsection
