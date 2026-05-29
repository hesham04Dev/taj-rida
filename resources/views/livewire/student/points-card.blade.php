<div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-6 h-full relative overflow-hidden">
    {{-- Background glow --}}
    <div class="absolute inset-0 bg-gradient-to-br from-emerald-500/5 to-transparent pointer-events-none rounded-2xl"></div>

    <div class="relative">
        <div class="flex items-center gap-3 mb-5">
            <div class="w-10 h-10 rounded-xl bg-emerald-500/10 flex items-center justify-center">
                <flux:icon.star class="size-5 text-emerald-400" />
            </div>
            <div>
                <h2 class="font-semibold text-white text-sm">نقاطك</h2>
                <p class="text-xs text-zinc-500">مجموع ما كسبته</p>
            </div>
        </div>

        {{-- Big points display --}}
        <div class="text-center my-6">
            <span class="text-6xl font-bold text-white tabular-nums">{{ number_format($totalPoints) }}</span>
            <p class="text-zinc-400 text-sm mt-2">نقطة مكتسبة</p>
        </div>

        {{-- Stats row --}}
        <div class="grid grid-cols-2 gap-3 mt-4">
            <div class="bg-zinc-800/60 rounded-xl p-3 text-center border border-zinc-700/50">
                <p class="text-xl font-bold text-emerald-400">{{ number_format($remainingPoints) }}</p>
                <p class="text-xs text-zinc-500 mt-0.5">متبقية</p>
            </div>
            <div class="bg-zinc-800/60 rounded-xl p-3 text-center border border-zinc-700/50">
                <p class="text-xl font-bold text-zinc-400">{{ number_format($givenPoints) }}</p>
                <p class="text-xs text-zinc-500 mt-0.5">تم صرفها</p>
            </div>
        </div>
    </div>
</div>
