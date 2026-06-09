<div class="space-y-4">
    <div>
        <label class="block text-sm font-medium">Title</label>
        <input name="title" value="{{ old('title', $week->title ?? '') }}" required class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm">
    </div>
    <div class="grid gap-4 sm:grid-cols-2">
        <div>
            <label class="block text-sm font-medium">Week number</label>
            <input type="number" name="week_number" value="{{ old('week_number', $week->week_number ?? '') }}" min="1" required class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm">
            <p class="mt-1 text-xs text-slate-500">Unique. Used for unlock order (Week 1 = first week).</p>
        </div>
        <div>
            <label class="block text-sm font-medium">Unlock after days</label>
            <input type="number" name="unlock_after_days" value="{{ old('unlock_after_days', $week->unlock_after_days ?? 0) }}" min="0" required class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm">
            <p class="mt-1 text-xs text-slate-500">Days after student registration. Week 1 = 0, Week 2 = 7, etc.</p>
        </div>
    </div>
    <div>
        <label class="block text-sm font-medium">Sort order</label>
        <input type="number" name="sort_order" value="{{ old('sort_order', $week->sort_order ?? 0) }}" min="0" required class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm">
        <p class="mt-1 text-xs text-slate-500">Display order on the student journey map.</p>
    </div>
    <div>
        <label class="block text-sm font-medium">Description</label>
        <textarea name="description" rows="3" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm">{{ old('description', $week->description ?? '') }}</textarea>
    </div>
    <label class="flex items-center gap-2 text-sm">
        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $week->is_active ?? true))>
        Active (visible on student journey)
    </label>
</div>
