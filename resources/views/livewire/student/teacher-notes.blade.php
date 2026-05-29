<div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-6 h-full">
    <div class="flex items-center gap-3 mb-5">
        <div class="w-10 h-10 rounded-xl bg-rose-500/10 flex items-center justify-center">
            <flux:icon.chat-bubble-left-ellipsis class="size-5 text-rose-400" />
        </div>
        <div>
            <h2 class="font-semibold text-white text-sm">ملاحظات المعلم</h2>
            <p class="text-xs text-zinc-500">آخر الملاحظات والتوجيهات</p>
        </div>
    </div>

    @if($notes->isEmpty())
        <div class="flex flex-col items-center justify-center py-8 text-center">
            <flux:icon.chat-bubble-left-ellipsis class="size-8 text-zinc-700 mb-2" />
            <p class="text-zinc-500 text-sm">لا توجد ملاحظات بعد</p>
        </div>
    @else
        <div class="space-y-3">
            @foreach($notes as $note)
                <div wire:key="note-{{ $note->id }}" class="p-4 rounded-xl bg-zinc-800/60 border border-zinc-700/50">
                    <div class="flex items-start justify-between gap-2 mb-2">
                        <span class="text-xs text-zinc-500">
                            {{ $note->date->translatedFormat('j F Y') }}
                        </span>
                        {{-- Rating dots (1-10 scaled to 5) --}}
                        <div class="flex gap-0.5" title="التقييم: {{ $note->rating }}/10">
                            @for($i = 1; $i <= 10; $i++)
                                <div class="w-1.5 h-1.5 rounded-full {{ $i <= $note->rating ? 'bg-emerald-500' : 'bg-zinc-700' }}"></div>
                            @endfor
                        </div>
                    </div>
                    <p class="text-sm text-zinc-300 leading-relaxed">{{ $note->description }}</p>
                </div>
            @endforeach
        </div>
    @endif
</div>
