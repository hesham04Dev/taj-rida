<div>
    {{-- Section Header --}}
    <div class="mb-5 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center">
                <flux:icon.bell class="size-5 text-indigo-600 dark:text-indigo-400" />
            </div>
            <div>
                <h2 class="font-bold text-zinc-900 dark:text-white text-base">الإشعارات</h2>
                @if($this->unreadCount > 0)
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ $this->unreadCount }} غير مقروءة</p>
                @else
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">جميع الإشعارات مقروءة</p>
                @endif
            </div>
        </div>
        @if($this->unreadCount > 0)
            <flux:button wire:click="markAllRead" variant="ghost" size="sm" class="text-indigo-600 dark:text-indigo-400 text-xs">
                تحديد الكل كمقروء
            </flux:button>
        @endif
    </div>

    @if($reads->isEmpty())
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl p-8 text-center">
            <flux:icon.bell class="size-10 text-zinc-300 dark:text-zinc-600 mx-auto mb-3" />
            <p class="text-zinc-500 dark:text-zinc-400 text-sm">لا توجد إشعارات حتى الآن</p>
        </div>
    @else
        <div class="space-y-3">
            @foreach($reads as $read)
                <div
                    wire:key="notif-{{ $read->id }}"
                    class="bg-white dark:bg-zinc-900 border rounded-2xl p-4 transition-all duration-200 flex items-start gap-4
                        {{ is_null($read->read_at)
                            ? 'border-indigo-300 dark:border-indigo-700 shadow-sm shadow-indigo-100 dark:shadow-indigo-900/20'
                            : 'border-zinc-200 dark:border-zinc-800 opacity-75' }}"
                >
                    {{-- Unread indicator --}}
                    <div class="mt-1 flex-shrink-0">
                        @if(is_null($read->read_at))
                            <div class="w-2.5 h-2.5 rounded-full bg-indigo-500 animate-pulse"></div>
                        @else
                            <div class="w-2.5 h-2.5 rounded-full bg-zinc-200 dark:bg-zinc-700"></div>
                        @endif
                    </div>

                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between gap-2 mb-1">
                            <h3 class="font-bold text-sm text-zinc-900 dark:text-white truncate">
                                {{ $read->notification->title }}
                            </h3>
                            <span class="text-xs text-zinc-400 dark:text-zinc-500 flex-shrink-0">
                                {{ $read->notification->created_at->diffForHumans() }}
                            </span>
                        </div>
                        <p class="text-sm text-zinc-600 dark:text-zinc-300 leading-relaxed">
                            {{ $read->notification->body }}
                        </p>
                        <p class="text-xs text-zinc-400 dark:text-zinc-500 mt-2">
                            من: {{ $read->notification->teacher->name }}
                        </p>
                    </div>

                    @if(is_null($read->read_at))
                        <button
                            wire:click="markRead({{ $read->id }})"
                            class="flex-shrink-0 text-xs text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-200 font-medium transition-colors"
                        >
                            تحديد كمقروء
                        </button>
                    @endif
                </div>
            @endforeach
        </div>
    @endif
</div>
