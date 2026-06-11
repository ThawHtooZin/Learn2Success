@extends('layouts.staff')

@section('title', 'Create user')

@section('content')
    <x-staff-nav-trail
        class="mb-4"
        :items="[
            ['label' => 'Users', 'url' => route('admin.users.index')],
            ['label' => 'Create user'],
        ]"
        title="Create user"
    />
    <form method="POST" action="{{ route('admin.users.store') }}" class="max-w-lg rounded-xl border border-slate-200 bg-white p-6">
        @csrf
        @include('admin.users._form')
        <button type="submit" class="mt-6 min-h-11 rounded-lg bg-slate-900 px-4 py-2.5 text-sm font-medium text-white">Save</button>
    </form>
@endsection
