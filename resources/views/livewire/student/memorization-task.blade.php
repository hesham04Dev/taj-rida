<div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-6 h-full">
    <div class="flex items-center gap-3 mb-5">
        <div class="w-10 h-10 rounded-xl bg-amber-500/10 flex items-center justify-center">
            <flux:icon.bookmark class="size-5 text-amber-400" />
        </div>
        <div>
            <h2 class="font-semibold text-white text-sm">مهمتك القادمة</h2>
            <p class="text-xs text-zinc-500">السور التي تحتاج متابعة</p>
        </div>
    </div>

    @if($tasks->isEmpty())
        <div class="flex flex-col items-center justify-center py-8 text-center">
            <div class="w-12 h-12 rounded-full bg-emerald-500/10 flex items-center justify-center mb-3">
                <flux:icon.check-circle class="size-6 text-emerald-400" />
            </div>
            <p class="text-emerald-400 font-medium text-sm">ممتاز! لا توجد سور تحتاج مراجعة الآن</p>
        </div>
    @else
        <div class="space-y-3">
            @foreach($tasks as $task)
                <div wire:key="task-{{ $task->id }}" class="flex items-start gap-3 p-3 rounded-xl bg-zinc-800/60 border border-zinc-700/50">
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-white text-sm">{{ $task->sura->name }}</p>
                        @if($task->need_from_page && $task->need_to_page)
                            <p class="text-xs text-zinc-400 mt-0.5">
                                الصفحات {{ $task->need_from_page }} — {{ $task->need_to_page }}
                            </p>
                        @endif
                    </div>
                    <div class="flex gap-1.5 flex-shrink-0">
                        @if($task->is_need_rememorisation)
                            <flux:badge color="amber" size="sm">حفظ</flux:badge>
                        @endif
                        @if($task->is_need_revision)
                            <flux:badge color="blue" size="sm">مراجعة</flux:badge>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
