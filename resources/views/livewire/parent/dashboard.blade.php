<div>
    {{-- Parent Welcome Banner --}}
    <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold dark:text-white">
                أهلاً بك، <span class="text-indigo-400">{{ Auth::guard('guardian')->user()->name }}</span> 👋
            </h1>
            <p class="text-zinc-400 text-sm mt-1">{{ now()->translatedFormat('l، j F Y') }}</p>
        </div>
    </div>

    {{-- Unread Notifications Alert --}}
    @if($unreadNotifications->isNotEmpty())
        <div class="mb-8">
            <h2 class="text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider mb-3">أحدث الإشعارات</h2>
            <div class="space-y-3">
                @foreach($unreadNotifications as $read)
                    <a href="{{ route('parent.notifications') }}" class="block bg-indigo-50/50 dark:bg-indigo-900/10 border border-indigo-100 dark:border-indigo-800/50 rounded-2xl p-4 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 transition-colors">
                        <div class="flex items-start gap-3">
                            <div class="mt-1 flex-shrink-0">
                                <div class="w-2.5 h-2.5 rounded-full bg-indigo-500 animate-pulse"></div>
                            </div>
                            <div>
                                <h3 class="font-bold text-sm text-indigo-900 dark:text-indigo-200">{{ $read->notification->title }}</h3>
                                <p class="text-sm text-indigo-800/70 dark:text-indigo-300/70 mt-0.5 line-clamp-1">{{ $read->notification->body }}</p>
                                <p class="text-xs text-indigo-600/60 dark:text-indigo-400/60 mt-1">
                                    من: {{ $read->notification->teacher->name }} &bull; {{ $read->notification->created_at->diffForHumans() }}
                                </p>
                            </div>
                        </div>
                    </a>
                @endforeach
                <div class="text-left mt-2">
                    <a href="{{ route('parent.notifications') }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 font-medium transition-colors">
                        عرض جميع الإشعارات &larr;
                    </a>
                </div>
            </div>
        </div>
    @endif

    @if($children->isEmpty())
        {{-- Empty state when parent has no registered children --}}
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl p-12 text-center max-w-xl mx-auto shadow-xl my-12">
            <div class="w-16 h-16 rounded-2xl bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center mx-auto mb-6">
                <flux:icon.user-group class="size-8 text-zinc-500 dark:text-zinc-400" />
            </div>
            <h2 class="text-xl font-bold text-zinc-900 dark:text-white mb-3">لا يوجد طلاب مرتبطون بحسابك</h2>
            <p class="text-zinc-500 dark:text-zinc-400 text-sm leading-relaxed mb-6">
                لم نجد أي طلاب مسجلين برقم هاتفك الحالي (<span class="text-indigo-500 dark:text-indigo-400 font-mono">{{ Auth::guard('guardian')->user()->phone }}</span>).
                يرجى الطلب من معلم الحلقة إضافة رقم هاتفك هذا إلى ملف الطالب لتتمكن من متابعته.
            </p>
        </div>
    @else
        {{-- Sibling Selector --}}
        @if($children->count() > 1)
            <div class="mb-8">
                <h2 class="text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider mb-3">اختر الابن للمتابعة</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                    @foreach($children as $child)
                        <button
                            wire:click="selectStudent({{ $child->id }})"
                            class="flex items-center gap-4 p-4 rounded-2xl text-right transition-all duration-300 border focus:outline-none {{ $selectedStudentId === $child->id ? 'bg-indigo-500/10 border-indigo-500 shadow-lg shadow-indigo-500/5' : 'bg-white dark:bg-zinc-900 border-zinc-200 dark:border-zinc-800 hover:border-zinc-300 dark:hover:border-zinc-700 shadow-xs' }}"
                        >
                            <div class="w-12 h-12 rounded-xl bg-gradient-to-br {{ $selectedStudentId === $child->id ? 'from-indigo-500 to-violet-600' : 'from-zinc-200 to-zinc-300 dark:from-zinc-800 dark:to-zinc-700 text-zinc-700 dark:text-zinc-300' }} flex items-center justify-center font-bold shadow-md">
                                {{ mb_substr($child->name, 0, 1) }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="font-bold text-sm {{ $selectedStudentId === $child->id ? 'text-indigo-600 dark:text-indigo-400' : 'text-zinc-900 dark:text-white' }} truncate">
                                    {{ $child->name }}
                                </p>
                                <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">
                                    النقاط: {{ $child->total_points }} ن
                                </p>
                            </div>
                            @if($selectedStudentId === $child->id)
                                <div class="w-5 h-5 rounded-full bg-indigo-600 dark:bg-indigo-500 flex items-center justify-center">
                                    <flux:icon.check class="size-3 text-white" />
                                </div>
                            @endif
                        </button>
                    @endforeach
                </div>
            </div>
        @else
            {{-- Single child header --}}
            <div class="mb-8 p-4 bg-zinc-100/40 dark:bg-zinc-900/40 border border-zinc-200 dark:border-zinc-900 rounded-2xl flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-indigo-500 to-violet-600 flex items-center justify-center text-white font-bold">
                    {{ mb_substr($selectedStudent->name, 0, 1) }}
                </div>
                <div>
                    <h2 class="font-bold text-zinc-900 dark:text-white text-base">متابعة الابن: <span class="text-indigo-600 dark:text-indigo-400">{{ $selectedStudent->name }}</span></h2>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">معرض لوحة المتابعة الشاملة لمستواه الدراسي وحفظه</p>
                </div>
            </div>
        @endif

        {{-- Dynamic Child Progress --}}
        @if($selectedStudent)
            <livewire:parent.child-progress :student="$selectedStudent" :key="$selectedStudent->id" />
        @endif
    @endif
</div>
