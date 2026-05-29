<div class="bg-zinc-900 border border-zinc-800 rounded-2xl p-6">
    <div class="flex items-center gap-3 mb-5">
        <div class="w-10 h-10 rounded-xl bg-purple-500/10 flex items-center justify-center">
            <flux:icon.academic-cap class="size-5 text-purple-400" />
        </div>
        <div>
            <h2 class="font-semibold text-white text-sm">درجات الأداء</h2>
            <p class="text-xs text-zinc-500">الحفظ والمراجعة</p>
        </div>
    </div>

    @if($memorizations->isEmpty())
        <p class="text-zinc-500 text-sm text-center py-4">لا توجد درجات مسجلة بعد</p>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            {{-- Memorization grades (Hifz) --}}
            <div class="space-y-2">
                <div class="flex items-center gap-2 mb-3">
                    <div class="w-2 h-2 rounded-full bg-emerald-500"></div>
                    <span class="text-xs font-semibold text-emerald-400 uppercase tracking-wide">الحفظ (هفظ)</span>
                </div>
                @forelse($memorizations->whereNotNull('memorization_degree') as $m)
                    <div wire:key="mem-{{ $m->id }}" class="flex items-center justify-between p-3 bg-zinc-800/60 rounded-xl border border-zinc-700/50">
                        <span class="text-sm text-zinc-300">{{ $m->sura->name }}</span>
                        @php
                            $deg = $m->memorization_degree;
                            $color = match(true) {
                                in_array($deg, ['ممتاز', 'Excellent']) => 'emerald',
                                in_array($deg, ['جيد جداً', 'Very Good']) => 'blue',
                                in_array($deg, ['جيد', 'Good']) => 'sky',
                                in_array($deg, ['مقبول', 'Pass']) => 'amber',
                                default => 'zinc',
                            };
                        @endphp
                        <flux:badge color="{{ $color }}" size="sm">{{ $deg }}</flux:badge>
                    </div>
                @empty
                    <p class="text-zinc-600 text-xs text-center py-2">لا يوجد</p>
                @endforelse
            </div>

            {{-- Revision grades (Muraja'a) --}}
            <div class="space-y-2">
                <div class="flex items-center gap-2 mb-3">
                    <div class="w-2 h-2 rounded-full bg-blue-500"></div>
                    <span class="text-xs font-semibold text-blue-400 uppercase tracking-wide">المراجعة (مراجعة)</span>
                </div>
                @forelse($memorizations->whereNotNull('revision_degree') as $m)
                    <div wire:key="rev-{{ $m->id }}" class="flex items-center justify-between p-3 bg-zinc-800/60 rounded-xl border border-zinc-700/50">
                        <span class="text-sm text-zinc-300">{{ $m->sura->name }}</span>
                        @php
                            $deg = $m->revision_degree;
                            $color = match(true) {
                                in_array($deg, ['ممتاز', 'Excellent']) => 'emerald',
                                in_array($deg, ['جيد جداً', 'Very Good']) => 'blue',
                                in_array($deg, ['جيد', 'Good']) => 'sky',
                                in_array($deg, ['مقبول', 'Pass']) => 'amber',
                                default => 'zinc',
                            };
                        @endphp
                        <flux:badge color="{{ $color }}" size="sm">{{ $deg }}</flux:badge>
                    </div>
                @empty
                    <p class="text-zinc-600 text-xs text-center py-2">لا يوجد</p>
                @endforelse
            </div>
        </div>
    @endif
</div>
