<div>
    {{-- Top row: Tasks + Points --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        
        {{-- Tasks Card --}}
        <div x-data="{ open: true }" class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl p-6 h-full shadow-sm dark:shadow-none transition-all duration-300">
            <div class="flex items-center justify-between" :class="open ? 'mb-5' : ''">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-amber-500/10 flex items-center justify-center">
                        <flux:icon.bookmark class="size-5 text-amber-500 dark:text-amber-400" />
                    </div>
                    <div>
                        <h2 class="font-semibold text-zinc-900 dark:text-white text-sm">المهمة القادمة للاختبار</h2>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400">السور التي تحتاج متابعة ومراجعة</p>
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

        {{-- Points Card --}}
        <div x-data="{ open: true }" class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl p-6 h-full relative overflow-hidden shadow-sm dark:shadow-none transition-all duration-300">
            <div class="absolute inset-0 bg-gradient-to-br from-emerald-500/5 to-transparent pointer-events-none rounded-2xl"></div>

            <div class="relative">
                <div class="flex items-center justify-between" :class="open ? 'mb-5' : ''">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-emerald-500/10 flex items-center justify-center">
                            <flux:icon.star class="size-5 text-emerald-500 dark:text-emerald-400" />
                        </div>
                        <div>
                            <h2 class="font-semibold text-zinc-900 dark:text-white text-sm">نقاط الطالب</h2>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400">رصيد النقاط المحرز</p>
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
                            <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">نقاط متوفرة</p>
                        </div>
                        <div class="bg-zinc-50 dark:bg-zinc-800/60 rounded-xl p-3 text-center border border-zinc-200 dark:border-zinc-700/50">
                            <p class="text-xl font-bold text-zinc-600 dark:text-zinc-400">{{ number_format($givenPoints) }}</p>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">تم صرفها كجوائز</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Grades row --}}
    <div x-data="{ open: true }" class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl p-6 mb-6 shadow-sm dark:shadow-none transition-all duration-300">
        <div class="flex items-center justify-between" :class="open ? 'mb-5' : ''">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-purple-500/10 flex items-center justify-center">
                    <flux:icon.academic-cap class="size-5 text-purple-500 dark:text-purple-400" />
                </div>
                <div>
                    <h2 class="font-semibold text-zinc-900 dark:text-white text-sm">تقييمات ودرجات السور</h2>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">مستوى الأداء في السور الأخيرة</p>
                </div>
            </div>
            <button @click="open = !open" class="text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-300 transition-colors focus:outline-none p-1 rounded-lg hover:bg-zinc-100 dark:hover:bg-zinc-800">
                <flux:icon.chevron-up x-show="open" class="size-4" />
                <flux:icon.chevron-down x-show="!open" class="size-4" />
            </button>
        </div>

        <div x-show="open" x-collapse>
            @if($memorizations->isEmpty())
                <p class="text-zinc-500 text-sm text-center py-4">لا توجد تقييمات مسجلة بعد</p>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    {{-- Memorization --}}
                    <div class="space-y-2">
                        <div class="flex items-center gap-2 mb-3">
                            <div class="w-2 h-2 rounded-full bg-emerald-500"></div>
                            <span class="text-xs font-semibold text-emerald-500 dark:text-emerald-400 tracking-wide">درجات الحفظ الجديد</span>
                        </div>
                        @forelse($memorizations->whereNotNull('memorization_degree') as $m)
                            <div wire:key="mem-{{ $m->id }}" class="flex items-center justify-between p-3 bg-zinc-50 dark:bg-zinc-800/60 rounded-xl border border-zinc-200 dark:border-zinc-700/50">
                                <span class="text-sm text-zinc-800 dark:text-zinc-300 font-medium">{{ $m->sura->name }}</span>
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
                            <p class="text-zinc-500 text-xs text-center py-4 bg-zinc-50 dark:bg-zinc-800/20 rounded-xl border border-dashed border-zinc-200 dark:border-zinc-800">لم يتم تسجيل درجات حفظ جديد بعد</p>
                        @endforelse
                    </div>

                    {{-- Revision --}}
                    <div class="space-y-2">
                        <div class="flex items-center gap-2 mb-3">
                            <div class="w-2 h-2 rounded-full bg-blue-500"></div>
                            <span class="text-xs font-semibold text-blue-500 dark:text-blue-400 tracking-wide">درجات المراجعة</span>
                        </div>
                        @forelse($memorizations->whereNotNull('revision_degree') as $m)
                            <div wire:key="rev-{{ $m->id }}" class="flex items-center justify-between p-3 bg-zinc-50 dark:bg-zinc-800/60 rounded-xl border border-zinc-200 dark:border-zinc-700/50">
                                <span class="text-sm text-zinc-855 dark:text-zinc-300 font-medium">{{ $m->sura->name }}</span>
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
                            <p class="text-zinc-500 text-xs text-center py-4 bg-zinc-50 dark:bg-zinc-800/20 rounded-xl border border-dashed border-zinc-200 dark:border-zinc-800">لم يتم تسجيل درجات مراجعة بعد</p>
                        @endforelse
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Bottom row: Attendance + Notes --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        {{-- Attendance Log --}}
        <div x-data="{ open: true }" class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl p-6 h-full shadow-sm dark:shadow-none transition-all duration-300">
            <div class="flex items-center justify-between mb-5">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-blue-500/10 flex items-center justify-center">
                        <flux:icon.calendar-days class="size-5 text-blue-500 dark:text-blue-400" />
                    </div>
                    <div>
                        <h2 class="font-semibold text-zinc-900 dark:text-white text-sm">سجل حضور الحصص</h2>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400">آخر {{ $attendanceRecords->count() }} حلقة</p>
                    </div>
                </div>

                {{-- Summary badges + Chevron --}}
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
                @if($attendanceRecords->isEmpty())
                    <p class="text-zinc-500 text-sm text-center py-4">لا توجد سجلات حضور بعد</p>
                @else
                    <div class="space-y-2 max-h-64 overflow-y-auto pl-1">
                        @foreach($attendanceRecords as $record)
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

        {{-- Teacher Notes --}}
        <div x-data="{ open: true }" class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl p-6 h-full shadow-sm dark:shadow-none transition-all duration-300">
            <div class="flex items-center justify-between" :class="open ? 'mb-5' : ''">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-rose-500/10 flex items-center justify-center">
                        <flux:icon.chat-bubble-left-ellipsis class="size-5 text-rose-500 dark:text-rose-400" />
                    </div>
                    <div>
                        <h2 class="font-semibold text-zinc-900 dark:text-white text-sm">توجيهات وملاحظات المعلم</h2>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400">متابعة السلوك والانضباط</p>
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
                        <p class="text-zinc-500 text-sm">لا توجد ملاحظات مسجلة بعد من الأستاذ</p>
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
    </div>
</div>
