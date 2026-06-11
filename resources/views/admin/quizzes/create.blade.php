@extends('layouts.staff')

@section('title', 'Create quiz')

@section('content')
    <x-staff-nav-trail
        class="mb-4"
        :items="[
            ['label' => 'Quizzes', 'url' => route('manage.quizzes.index')],
            ['label' => 'Create quiz'],
        ]"
        title="Create quiz"
    />
    <form method="POST" action="{{ route('manage.quizzes.store') }}" class="rounded-xl border border-slate-200 bg-white p-6">
        @csrf
        @include('admin.quizzes._form')
    </form>
@endsection
