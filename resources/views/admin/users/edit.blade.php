@extends('layouts.staff')

@section('title', 'Edit user')

@section('content')
    <x-staff-nav-trail
        class="mb-4"
        :items="[
            ['label' => 'Users', 'url' => route('admin.users.index')],
            ['label' => $user->username],
        ]"
        title="Edit user"
    />
    <form method="POST" action="{{ route('admin.users.update', $user) }}" class="max-w-lg rounded-xl border border-slate-200 bg-white p-6">
        @csrf
        @method('PUT')
        @include('admin.users._form', ['user' => $user])
        <button type="submit" class="mt-6 min-h-11 rounded-lg bg-slate-900 px-4 py-2.5 text-sm font-medium text-white">Update</button>
    </form>
@endsection
