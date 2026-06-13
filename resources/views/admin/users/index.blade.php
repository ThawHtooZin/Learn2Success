@extends('layouts.staff')

@section('title', 'Users — '.config('app.name'))

@section('content')
    <div class="mb-4 flex items-center justify-between">
        <h1 class="text-xl font-semibold">Users</h1>
        <a href="{{ route('admin.users.create') }}" class="min-h-11 rounded-lg bg-slate-900 px-4 py-2.5 text-sm font-medium text-white hover:bg-slate-800">Create user</a>
    </div>

    <x-data-table>
        <x-slot:toolbar>
            <x-data-table.toolbar :table="$tableQuery" search-placeholder="Search username…">
                <x-slot:filters>
                    <x-data-table.filter-select
                        label="Role"
                        name="role"
                        :value="request('role')"
                        :options="['' => 'All roles', 'student' => 'Student', 'teacher' => 'Teacher', 'admin' => 'Admin']"
                    />
                </x-slot:filters>
            </x-data-table.toolbar>
        </x-slot:toolbar>

        <x-slot:head>
            <tr>
                <x-data-table.sort-header column="username" label="Username" :table="$tableQuery" />
                <x-data-table.sort-header column="role" label="Role" :table="$tableQuery" />
                <th class="px-4 py-3 text-right font-medium">Actions</th>
            </tr>
        </x-slot:head>

        @forelse ($users as $user)
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
        @empty
            <tr>
                <td colspan="3" class="px-4 py-8 text-center text-slate-500">No users match your search or filters.</td>
            </tr>
        @endforelse

        <x-slot:footer>
            <x-data-table.footer :paginator="$users" />
        </x-slot:footer>
    </x-data-table>
@endsection
