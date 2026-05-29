<div x-data="{ open: true }" class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl p-6 h-full shadow-sm dark:shadow-none transition-all duration-300">
    <div class="flex items-center justify-between" :class="open ? 'mb-5' : ''">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-rose-500/10 flex items-center justify-center">
                <flux:icon.chat-bubble-left-ellipsis class="size-5 text-rose-500 dark:text-rose-400" />
            </div>
            <div>
                <h2 class="font-semibold text-zinc-900 dark:text-white text-sm">ملاحظات المعلم</h2>
                <p class="text-xs text-zinc-500 dark:text-zinc-400">آخر الملاحظات والتوجيهات</p>
            </div>
        </div>
        <button @click="open = !open" class="text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-300 transition-colors focus:outline-none p-1 rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-800">
            <flux:icon.chevron-up x-show="open" class="size-4" />
            <flux:icon.chevron-down x-show="!open" class="size-4" />
        </button>
    </div>

    <div x-show="open" x-collapse>
        @if($notes->isEmpty())
            <div class="flex flex-col items-center justify-center py-8 text-center">
                <flux:icon.chat-bubble-left-ellipsis class="size-8 text-zinc-300 dark:text-zinc-700 mb-2" />
                <p class="text-zinc-500 text-sm">لا توجد ملاحظات بعد</p>
            </div>
        @else
            <div class="space-y-3">
                @foreach($notes as $note)
                    <div wire:key="note-{{ $note->id }}" class="p-4 rounded-xl bg-zinc-50 dark:bg-zinc-800/60 border border-zinc-200 dark:border-zinc-700/50">
                        <div class="flex items-start justify-between gap-2 mb-2">
                            <span class="text-xs text-zinc-500">
                                {{ $note->date->translatedFormat('j F Y') }}
                            </span>
                            {{-- Rating dots (1-10 scaled to 10 dots) --}}
                            <div class="flex gap-0.5" title="التقييم: {{ $note->rating }}/10">
                                @for($i = 1; $i <= 10; $i++)
                                    <div class="w-1.5 h-1.5 rounded-full {{ $i <= $note->rating ? 'bg-emerald-500' : 'bg-zinc-300 dark:bg-zinc-700' }}"></div>
                                @endfor
                            </div>
                        </div>
                        <p class="text-sm text-zinc-800 dark:text-zinc-300 leading-relaxed">{{ $note->description }}</p>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
