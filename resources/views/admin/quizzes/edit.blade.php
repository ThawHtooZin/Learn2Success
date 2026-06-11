@extends('layouts.staff')

@section('title', 'Edit quiz')

@section('content')
    <x-staff-nav-trail
        class="mb-4"
        :items="[
            ['label' => 'Quizzes', 'url' => route('manage.quizzes.index')],
            ['label' => $quiz->title],
        ]"
        title="Edit quiz"
    />
    <form method="POST" action="{{ route('manage.quizzes.update', $quiz) }}" class="rounded-xl border border-slate-200 bg-white p-6">
        @csrf
        @method('PUT')
        @include('admin.quizzes._form', ['quiz' => $quiz])
    </form>
@endsection
