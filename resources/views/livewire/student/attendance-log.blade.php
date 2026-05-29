<div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-6 h-full">
    <div class="flex items-center justify-between mb-5">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-blue-500/10 flex items-center justify-center">
                <flux:icon.calendar-days class="size-5 text-blue-400" />
            </div>
            <div>
                <h2 class="font-semibold text-white text-sm">سجل الحضور</h2>
                <p class="text-xs text-zinc-500">آخر {{ $records->count() }} حصة</p>
            </div>
        </div>

        {{-- Summary badges --}}
        <div class="flex gap-2">
            <span class="flex items-center gap-1 text-xs bg-emerald-500/10 text-emerald-400 px-2 py-1 rounded-lg">
                <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full"></span>
                {{ $presentCount }} حضور
            </span>
            <span class="flex items-center gap-1 text-xs bg-red-500/10 text-red-400 px-2 py-1 rounded-lg">
                <span class="w-1.5 h-1.5 bg-red-500 rounded-full"></span>
                {{ $absentCount }} غياب
            </span>
        </div>
    </div>

    @if($records->isEmpty())
        <p class="text-zinc-500 text-sm text-center py-4">لا توجد سجلات حضور بعد</p>
    @else
        <div class="space-y-2 max-h-64 overflow-y-auto pl-1">
            @foreach($records as $record)
                <div wire:key="att-{{ $record->id }}" class="flex items-center justify-between p-3 rounded-xl {{ $record->is_present ? 'bg-emerald-500/5 border border-emerald-500/20' : 'bg-red-500/5 border border-red-500/20' }}">
                    <span class="text-sm text-zinc-300">
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
