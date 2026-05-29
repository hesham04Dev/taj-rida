<div x-data="{ open: true }" class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl p-6 h-full shadow-sm dark:shadow-none transition-all duration-300">
    <div class="flex items-center justify-between" :class="open ? 'mb-5' : ''">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-amber-500/10 flex items-center justify-center">
                <flux:icon.bookmark class="size-5 text-amber-500 dark:text-amber-400" />
            </div>
            <div>
                <h2 class="font-semibold text-zinc-900 dark:text-white text-sm">مهمتك القادمة</h2>
                <p class="text-xs text-zinc-500 dark:text-zinc-400">السور التي تحتاج متابعة</p>
            </div>
        </div>
        <button @click="open = !open" class="text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-300 transition-colors focus:outline-none p-1 rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-800">
            <flux:icon.chevron-up x-show="open" class="size-4" />
            <flux:icon.chevron-down x-show="!open" class="size-4" />
        </button>
    </div>

    <div x-show="open" x-collapse>
        @if($tasks->isEmpty())
            <div class="flex flex-col items-center justify-center py-8 text-center">
                <div class="w-12 h-12 rounded-full bg-emerald-500/10 flex items-center justify-center mb-3">
                    <flux:icon.check-circle class="size-6 text-emerald-500 dark:text-emerald-400" />
                </div>
                <p class="text-emerald-600 dark:text-emerald-400 font-medium text-sm">ممتاز! لا توجد سور تحتاج مراجعة الآن</p>
            </div>
        @else
            <div class="space-y-3">
                @foreach($tasks as $task)
                    <div wire:key="task-{{ $task->id }}" class="flex items-start gap-3 p-3 rounded-xl bg-zinc-50 dark:bg-zinc-800/60 border border-zinc-200 dark:border-zinc-700/50">
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-zinc-900 dark:text-white text-sm">{{ $task->sura->name }}</p>
                            @if($task->need_from_page && $task->need_to_page)
                                <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">
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
</div>
