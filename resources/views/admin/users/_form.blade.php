@php
    $roles = \App\Enums\UserRole::cases();
@endphp

<div>
    <label class="block text-sm font-medium">Username</label>
    <input name="username" value="{{ old('username', $user->username ?? '') }}" required class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm">
    @error('username')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
</div>

<div class="mt-4">
    <label class="block text-sm font-medium">Password {{ isset($user) ? '(leave blank to keep)' : '' }}</label>
    <input type="password" name="password" {{ isset($user) ? '' : 'required' }} class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm">
    @error('password')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
</div>

<div class="mt-4">
    <label class="block text-sm font-medium">Confirm password</label>
    <input type="password" name="password_confirmation" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm">
</div>

<div class="mt-4">
    <label class="block text-sm font-medium">Role</label>
    <select name="role" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm">
        @foreach ($roles as $role)
            <option value="{{ $role->value }}" @selected(old('role', $user->role->value ?? '') === $role->value)>{{ ucfirst($role->value) }}</option>
        @endforeach
    </select>
    @error('role')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
</div>
