@extends('layouts.staff')

@section('title', 'Edit quiz')

@section('content')
    <h1 class="mb-4 text-xl font-semibold">Edit quiz</h1>
    <form method="POST" action="{{ route('manage.quizzes.update', $quiz) }}" class="rounded-xl border border-slate-200 bg-white p-6">
        @csrf
        @method('PUT')
        @include('admin.quizzes._form', ['quiz' => $quiz])
    </form>
@endsection
