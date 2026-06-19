<div wire:poll.5s>
    {{-- Section Header --}}
    <div class="mb-6 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-violet-100 dark:bg-violet-900/30 flex items-center justify-center">
                <flux:icon.chat-bubble-left-right class="size-5 text-violet-600 dark:text-violet-400" />
            </div>
            <div>
                <h1 class="font-bold text-zinc-900 dark:text-white text-xl">المحادثات</h1>
                @if($this->totalUnreadCount > 0)
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ $this->totalUnreadCount }} رسالة غير مقروءة</p>
                @else
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">لا توجد رسائل جديدة</p>
                @endif
            </div>
        </div>

        {{-- New Conversation Button --}}
        @if(!empty($availableContacts))
            <flux:button
                wire:click="$toggle('showNewConversation')"
                variant="primary"
                size="sm"
                icon="plus"
            >
                محادثة جديدة
            </flux:button>
        @endif
    </div>

    {{-- New Conversation Picker --}}
    @if($showNewConversation && !empty($availableContacts))
        <div class="mb-6 bg-violet-50 dark:bg-violet-900/20 border border-violet-200 dark:border-violet-700 rounded-2xl p-5">
            <h3 class="font-bold text-sm text-violet-900 dark:text-violet-200 mb-4 flex items-center gap-2">
                <flux:icon.plus-circle class="size-4" />
                ابدأ محادثة جديدة مع معلم طفلك
            </h3>
            <div class="space-y-2">
                @foreach($availableContacts as $contact)
                    <button
                        wire:click="startConversation({{ $contact['student_id'] }}, {{ $contact['teacher_id'] }})"
                        class="w-full flex items-center gap-4 p-3 bg-white dark:bg-zinc-900 border border-violet-200 dark:border-violet-700 rounded-xl hover:bg-violet-50 dark:hover:bg-violet-900/30 transition-colors text-right"
                    >
                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-violet-500 to-indigo-600 flex items-center justify-center text-white font-bold text-sm flex-shrink-0">
                            {{ mb_substr($contact['teacher_name'], 0, 1) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-sm text-zinc-900 dark:text-white">{{ $contact['teacher_name'] }}</p>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400">بخصوص: {{ $contact['student_name'] }}</p>
                        </div>
                        <flux:icon.arrow-left class="size-4 text-violet-400 flex-shrink-0" />
                    </button>
                @endforeach
            </div>
        </div>
    @endif

    @if($conversations->isEmpty() && empty($availableContacts))
        {{-- No conversations and no available contacts --}}
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl p-10 text-center">
            <flux:icon.chat-bubble-left-right class="size-10 text-zinc-300 dark:text-zinc-600 mx-auto mb-3" />
            <p class="text-zinc-600 dark:text-zinc-400 font-medium mb-1">لا توجد محادثات بعد</p>
            <p class="text-zinc-400 dark:text-zinc-500 text-sm">سيتواصل معك المعلم قريباً</p>
        </div>
    @elseif($conversations->isEmpty())
        {{-- No conversations yet but has contacts to start with --}}
        <div class="bg-white dark:bg-zinc-900 border border-dashed border-violet-300 dark:border-violet-700 rounded-2xl p-10 text-center">
            <flux:icon.chat-bubble-left-right class="size-10 text-violet-300 dark:text-violet-700 mx-auto mb-3" />
            <p class="text-zinc-600 dark:text-zinc-400 font-medium mb-2">لا توجد محادثات بعد</p>
            <p class="text-zinc-400 dark:text-zinc-500 text-sm mb-4">يمكنك بدء محادثة مع معلم طفلك الآن</p>
            <flux:button wire:click="$set('showNewConversation', true)" variant="primary" size="sm" icon="plus">
                ابدأ محادثة
            </flux:button>
        </div>
    @else
        <div class="flex gap-4 h-[55vh]">
            {{-- Conversations list --}}
            <div class="w-60 flex-shrink-0 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-2xl overflow-y-auto">
                <ul class="divide-y divide-zinc-100 dark:divide-zinc-800">
                    
                    @foreach($conversations as $conv)
                        @php
                            $lastMsg = $conv->messages->first();
                            $unread = $conv->messages->where('sender_type', 'teacher')->whereNull('read_at')->count();
                        @endphp
                        <li wire:key="conv-{{ $conv->id }}">
                            <button
                                wire:click="selectConversation({{ $conv->id }})"
                                class="w-full text-right p-4 hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors
                                    {{ $activeConversationId === $conv->id ? 'bg-violet-50 dark:bg-violet-900/20 border-r-2 border-violet-500' : '' }}"
                            >
                                <div class="flex items-center justify-between gap-2">
                                    <span class="font-semibold text-sm text-zinc-800 dark:text-zinc-100 truncate">
                                        {{ $conv->teacher->name }}
                                    </span>
                                    @if($unread > 0)
                                        <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-violet-600 text-white text-xs font-bold flex-shrink-0">
                                            {{ $unread }}
                                        </span>
                                    @endif
                                </div>
                                <p class="text-xs text-zinc-400 dark:text-zinc-500 mt-0.5 truncate">
                                    بخصوص: {{ $conv?->student?->name ??""}}
                                    {{-- {{ dd($conv) }} --}}
                                </p>
                                @if($lastMsg)
                                    <p class="text-xs text-zinc-400 dark:text-zinc-500 mt-1 truncate">
                                        {{ Str::limit($lastMsg->body, 35) }}
                                    </p>
                                @endif
                            </button>
                        </li>
                    @endforeach
                </ul>
            </div>

            {{-- Thread --}}
            <div class="flex-1 flex flex-col bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-2xl overflow-hidden">
                @php $active = $this->getActiveConversation(); @endphp

                @if(! $active)
                    <div class="flex-1 flex flex-col items-center justify-center text-zinc-400 dark:text-zinc-500 gap-2">
                        <flux:icon.chat-bubble-left-right class="size-8 text-zinc-300 dark:text-zinc-700" />
                        <p class="text-sm">اختر محادثة من القائمة</p>
                    </div>
                @else
                    {{-- Thread Header --}}
                    <div class="px-5 py-4 border-b border-zinc-200 dark:border-zinc-700 flex items-center gap-3">
                        <div class="w-9 h-9 rounded-full bg-violet-100 dark:bg-violet-900 flex items-center justify-center text-violet-600 dark:text-violet-300 font-bold text-sm">
                            {{ mb_substr($active->teacher->name, 0, 1) }}
                        </div>
                        <div>
                            <p class="font-bold text-sm text-zinc-900 dark:text-white">{{ $active->teacher->name }}</p>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400">بخصوص: {{ $active->student->name }}</p>
                        </div>
                    </div>

                    {{-- Messages --}}
                    <div class="flex-1 overflow-y-auto p-4 space-y-3" id="parent-thread-messages">
                        @forelse($active->messages as $msg)
                            <div wire:key="msg-{{ $msg->id }}" class="flex {{ $msg->isFromGuardian() ? 'justify-end' : 'justify-start' }}">
                                <div class="max-w-[70%] px-4 py-2 rounded-2xl text-sm leading-relaxed
                                    {{ $msg->isFromGuardian()
                                        ? 'bg-violet-600 text-white rounded-bl-none'
                                        : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-800 dark:text-zinc-100 rounded-br-none' }}">
                                    {{ $msg->body }}
                                    <div class="text-[10px] mt-1 opacity-60">
                                        {{ $msg->created_at->format('H:i') }}
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="flex-1 flex items-center justify-center text-zinc-400 text-sm py-8">
                                ابدأ المحادثة بإرسال رسالة
                            </div>
                        @endforelse
                    </div>

                    {{-- Compose --}}
                    <div class="border-t border-zinc-200 dark:border-zinc-700 p-3 flex gap-2 items-end">
                        <textarea
                            wire:model.live="newMessage"
                            wire:keydown.ctrl.enter="sendMessage"
                            rows="2"
                            placeholder="اكتب رسالتك... (Ctrl+Enter للإرسال)"
                            class="flex-1 rounded-xl border border-zinc-300 dark:border-zinc-600 bg-zinc-50 dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 text-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-violet-500 resize-none"
                        ></textarea>
                        <flux:button wire:click="sendMessage" variant="primary" size="sm" icon="paper-airplane">
                            إرسال
                        </flux:button>
                    </div>
                @endif
            </div>
        </div>
    @endif

    <script>
        document.addEventListener('livewire:update', function() {
            const el = document.getElementById('parent-thread-messages');
            if (el) { el.scrollTop = el.scrollHeight; }
        });
    </script>
</div>
