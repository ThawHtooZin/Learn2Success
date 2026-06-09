@extends('layouts.staff')

@section('title', 'Create week — '.config('app.name'))

@section('content')
    <h1 class="mb-4 text-xl font-semibold">Create week</h1>
    <form method="POST" action="{{ route('admin.weeks.store') }}" class="max-w-2xl rounded-xl border border-slate-200 bg-white p-6">
        @csrf
        @include('admin.weeks._form')
        <button type="submit" class="mt-6 min-h-11 rounded-lg bg-slate-900 px-4 py-2.5 text-sm font-medium text-white">Save week</button>
    </form>
@endsection
