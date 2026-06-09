@extends('layouts.staff')

@section('title', 'Users — '.config('app.name'))

@section('content')
    <div class="mb-4 flex items-center justify-between">
        <h1 class="text-xl font-semibold">Users</h1>
        <a href="{{ route('admin.users.create') }}" class="min-h-11 rounded-lg bg-slate-900 px-4 py-2.5 text-sm font-medium text-white hover:bg-slate-800">Create user</a>
    </div>

    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-50 text-left">
                <tr>
                    <th class="px-4 py-3 font-medium">Username</th>
                    <th class="px-4 py-3 font-medium">Role</th>
                    <th class="px-4 py-3 font-medium text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $user)
                    <tr class="border-t border-slate-100">
                        <td class="px-4 py-3">{{ $user->username }}</td>
                        <td class="px-4 py-3 capitalize">{{ $user->role->value }}</td>
                        <td class="px-4 py-3 text-right space-x-2">
                            <a href="{{ route('admin.users.edit', $user) }}" class="font-medium hover:underline">Edit</a>
                            <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="inline" onsubmit="return confirm('Delete this user?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="font-medium text-red-600 hover:underline">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $users->links() }}</div>
@endsection
