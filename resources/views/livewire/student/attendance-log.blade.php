<div x-data="{ open: true }" class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl p-6 h-full shadow-sm dark:shadow-none transition-all duration-300">
    <div class="flex items-center justify-between" :class="open ? 'mb-5' : ''">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-blue-500/10 flex items-center justify-center">
                <flux:icon.calendar-days class="size-5 text-blue-500 dark:text-blue-400" />
            </div>
            <div>
                <h2 class="font-semibold text-zinc-900 dark:text-white text-sm">سجل الحضور</h2>
                <p class="text-xs text-zinc-500 dark:text-zinc-400">آخر {{ $records->count() }} حصة</p>
            </div>
        </div>

        {{-- Summary badges + chevron --}}
        <div class="flex items-center gap-3">
            <div class="flex gap-2" x-show="open" x-collapse.horizontal>
                <span class="flex items-center gap-1 text-xs bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 px-2 py-1 rounded-lg">
                    <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full"></span>
                    {{ $presentCount }} حضور
                </span>
                <span class="flex items-center gap-1 text-xs bg-red-500/10 text-red-600 dark:text-red-400 px-2 py-1 rounded-lg">
                    <span class="w-1.5 h-1.5 bg-red-500 rounded-full"></span>
                    {{ $absentCount }} غياب
                </span>
            </div>
            <button @click="open = !open" class="text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-300 transition-colors focus:outline-none p-1 rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-800">
                <flux:icon.chevron-up x-show="open" class="size-4" />
                <flux:icon.chevron-down x-show="!open" class="size-4" />
            </button>
        </div>
    </div>

    <div x-show="open" x-collapse>
        @if($records->isEmpty())
            <p class="text-zinc-500 text-sm text-center py-4">لا توجد سجلات حضور بعد</p>
        @else
            <div class="space-y-2 max-h-64 overflow-y-auto pl-1">
                @foreach($records as $record)
                    <div wire:key="att-{{ $record->id }}" class="flex items-center justify-between p-3 rounded-xl {{ $record->is_present ? 'bg-emerald-500/5 border border-emerald-500/20' : 'bg-red-500/5 border border-red-500/20' }}">
                        <span class="text-sm text-zinc-800 dark:text-zinc-300">
                            {{ $record->date->translatedFormat('l، j M') }}
                        </span>
                        @if($record->is_present)
                            <flux:badge color="emerald" size="sm">حاضر ✓</flux:badge>
                        @else
                            <flux:badge color="red" size="sm">غائب ✗</flux:badge>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
