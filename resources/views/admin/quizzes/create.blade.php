@extends('layouts.staff')

@section('title', 'Create quiz')

@section('content')
    <h1 class="mb-4 text-xl font-semibold">Create quiz</h1>
    <form method="POST" action="{{ route('manage.quizzes.store') }}" class="rounded-xl border border-slate-200 bg-white p-6">
        @csrf
        @include('admin.quizzes._form')
    </form>
@endsection
