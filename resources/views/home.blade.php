@extends('layouts.guest')

@section('title', config('app.name'))

@section('content')
    <main class="min-h-screen w-full bg-gradient-to-b from-[#E3F2FD] to-[#f6fafe]">
        <section class="w-full px-4 pb-6 pt-8">
            <div class="text-center">
                <img
                    src="{{ asset(config('branding.logo')) }}"
                    alt="{{ config('app.name') }}"
                    class="mx-auto h-28 w-28 object-contain drop-shadow-md"
                >
                <h1 class="mt-4 text-4xl font-bold tracking-tight text-[#785900]">{{ config('app.name') }}</h1>
                <p class="mt-3 text-xl font-semibold leading-relaxed text-[#171c1f]">
                    {{ config('branding.tagline') }}
                </p>
            </div>

            <div class="mt-8">
                <x-image-carousel />
            </div>

            <div class="mt-8">
                <a href="{{ route('login') }}" class="guest-btn-primary">
                    Sign in to start
                </a>
            </div>
        </section>

        <section class="w-full space-y-4 px-4 pb-12">
            <div class="student-card text-center">
                <div class="text-4xl">🎧</div>
                <h2 class="mt-3 text-xl font-bold text-[#785900]">Listen</h2>
                <p class="mt-2 text-base font-medium text-[#4f4632]">Hear questions with a big friendly Listen button.</p>
            </div>
            <div class="student-card text-center">
                <div class="text-4xl">🎤</div>
                <h2 class="mt-3 text-xl font-bold text-[#785900]">Speak</h2>
                <p class="mt-2 text-base font-medium text-[#4f4632]">Record your voice right in the browser.</p>
            </div>
            <div class="student-card text-center">
                <div class="text-4xl">🏆</div>
                <h2 class="mt-3 text-xl font-bold text-[#785900]">Succeed</h2>
                <p class="mt-2 text-base font-medium text-[#4f4632]">Earn marks and celebrate your progress.</p>
            </div>
        </section>
    </main>
@endsection
