<div x-data="{ open: true }" class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl p-6 h-full relative overflow-hidden shadow-sm dark:shadow-none transition-all duration-300">
    {{-- Background glow --}}
    <div class="absolute inset-0 bg-gradient-to-br from-emerald-500/5 to-transparent pointer-events-none rounded-2xl"></div>

    <div class="relative">
        <div class="flex items-center justify-between" :class="open ? 'mb-5' : ''">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-500/10 flex items-center justify-center">
                    <flux:icon.star class="size-5 text-emerald-500 dark:text-emerald-400" />
                </div>
                <div>
                    <h2 class="font-semibold text-zinc-900 dark:text-white text-sm">نقاطك</h2>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">مجموع ما كسبته</p>
                </div>
            </div>
            <button @click="open = !open" class="text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-300 transition-colors focus:outline-none p-1 rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-800">
                <flux:icon.chevron-up x-show="open" class="size-4" />
                <flux:icon.chevron-down x-show="!open" class="size-4" />
            </button>
        </div>

        <div x-show="open" x-collapse>
            {{-- Big points display --}}
            <div class="text-center my-6">
                <span class="text-6xl font-bold text-zinc-900 dark:text-white tabular-nums">{{ number_format($totalPoints) }}</span>
                <p class="text-zinc-500 dark:text-zinc-400 text-sm mt-2">نقطة مكتسبة</p>
            </div>

            {{-- Stats row --}}
            <div class="grid grid-cols-2 gap-3 mt-4">
                <div class="bg-zinc-50 dark:bg-zinc-800/60 rounded-xl p-3 text-center border border-zinc-200 dark:border-zinc-700/50">
                    <p class="text-xl font-bold text-emerald-600 dark:text-emerald-400">{{ number_format($remainingPoints) }}</p>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">متبقية</p>
                </div>
                <div class="bg-zinc-50 dark:bg-zinc-800/60 rounded-xl p-3 text-center border border-zinc-200 dark:border-zinc-700/50">
                    <p class="text-xl font-bold text-zinc-600 dark:text-zinc-400">{{ number_format($givenPoints) }}</p>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">تم صرفها</p>
                </div>
            </div>
        </div>
    </div>
</div>
