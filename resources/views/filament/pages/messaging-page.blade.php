<x-filament-panels::page>
    @vite('resources/css/app.css')
    {{-- Header Actions --}}
    <div class="flex justify-between items-center mb-4">
        <div></div>
        @if(!empty($this->availableContacts))
            <button
                wire:click="$toggle('showNewConversation')"
                class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-sm font-semibold transition-colors flex items-center gap-2 shadow-sm"
            >
                <x-heroicon-o-plus-circle class="w-5 h-5" />
                محادثة جديدة
            </button>
        @endif
    </div>

    {{-- New Conversation Picker --}}
    @if($showNewConversation && !empty($availableContacts))
        <div class="mb-6 bg-indigo-50 dark:bg-indigo-900/20 border border-indigo-200 dark:border-indigo-700 rounded-xl p-5 shadow-sm">
            <h3 class="font-bold text-sm text-indigo-900 dark:text-indigo-200 mb-4 flex items-center gap-2">
                <x-heroicon-o-chat-bubble-left-right class="w-5 h-5" />
                ابدأ محادثة مع ولي أمر
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                @foreach($availableContacts as $contact)
                    <button
                        wire:click="startConversation({{ $contact['student_id'] }}, {{ $contact['guardian_id'] }})"
                        class="w-full flex items-center gap-4 p-4 bg-white dark:bg-zinc-900 border border-indigo-200 dark:border-indigo-700 rounded-xl hover:bg-indigo-50 dark:hover:bg-indigo-900/30 transition-colors text-right shadow-sm"
                    >
                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-500 to-violet-600 flex items-center justify-center text-white font-bold text-sm flex-shrink-0">
                            {{ mb_substr($contact['guardian_name'], 0, 1) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-sm text-zinc-900 dark:text-white truncate">{{ $contact['guardian_name'] }}</p>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1 truncate">ولي أمر: {{ $contact['student_name'] }}</p>
                        </div>
                        <x-heroicon-o-arrow-left class="w-5 h-5 text-indigo-400 flex-shrink-0" />
                    </button>
                @endforeach
            </div>
        </div>
    @endif

    @php $conversations = $this->getConversations(); @endphp

    @if($conversations->isEmpty() && empty($availableContacts))
        {{-- No conversations and no available contacts --}}
        <div class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl p-10 text-center shadow-sm">
            <x-heroicon-o-chat-bubble-left-right class="w-12 h-12 text-zinc-300 dark:text-zinc-600 mx-auto mb-4" />
            <p class="text-zinc-600 dark:text-zinc-400 font-medium mb-1">لا توجد محادثات بعد</p>
            <p class="text-zinc-400 dark:text-zinc-500 text-sm">ليس لديك طلاب مرتبطون بأولياء أمور حالياً</p>
        </div>
    @elseif($conversations->isEmpty())
        {{-- No conversations yet but has contacts to start with --}}
        <div class="bg-white dark:bg-zinc-900 border border-dashed border-indigo-300 dark:border-indigo-700 rounded-xl p-10 text-center shadow-sm">
            <x-heroicon-o-chat-bubble-left-right class="w-12 h-12 text-indigo-300 dark:text-indigo-700 mx-auto mb-4" />
            <p class="text-zinc-600 dark:text-zinc-400 font-medium mb-2">لا توجد محادثات بعد</p>
            <p class="text-zinc-400 dark:text-zinc-500 text-sm mb-5">يمكنك بدء محادثة مع ولي أمر أحد طلابك</p>
            <button wire:click="$set('showNewConversation', true)" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-sm font-semibold transition-colors inline-flex items-center gap-2">
                <x-heroicon-o-plus-circle class="w-5 h-5" />
                ابدأ محادثة
            </button>
        </div>
    @else
        <div class="flex gap-4 h-[70vh]" wire:poll.3s="getConversations">
            {{-- Conversations Sidebar --}}
            <div class="w-72 flex-shrink-0 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-xl overflow-y-auto shadow-sm flex flex-col">
                <div class="p-4 border-b border-zinc-200 dark:border-zinc-700 sticky top-0 bg-white dark:bg-zinc-900 z-10">
                    <h2 class="font-bold text-zinc-900 dark:text-white text-sm">المحادثات</h2>
                </div>

                <ul class="divide-y divide-zinc-100 dark:divide-zinc-800 flex-1">
                    @foreach($conversations as $conv)
                        @php
                            $lastMsg = $conv->messages->first();
                            $unread = $conv->messages->where('sender_type', 'guardian')->whereNull('read_at')->count();
                        @endphp
                        <li>
                            <button
                                wire:click="selectConversation({{ $conv->id }})"
                                class="w-full text-right p-4 hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors {{ $activeConversationId === $conv->id ? 'bg-indigo-50 dark:bg-indigo-900/20 border-r-2 border-indigo-500' : '' }}"
                            >
                                <div class="flex items-center justify-between gap-2">
                                    <span class="font-semibold text-sm text-zinc-800 dark:text-zinc-100 truncate">
                                        {{ $conv->guardian->name }}
                                    </span>
                                    @if($unread > 0)
                                        <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-indigo-600 text-white text-xs font-bold">
                                            {{ $unread }}
                                        </span>
                                    @endif
                                </div>
                                <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1 truncate">
                                    الطالب: {{ $conv->student->name }}
                                </p>
                                @if($lastMsg)
                                    <p class="text-xs text-zinc-400 dark:text-zinc-500 mt-1 truncate">
                                        {{ Str::limit($lastMsg->body, 40) }}
                                    </p>
                                @endif
                            </button>
                        </li>
                    @endforeach
                </ul>
            </div>

            {{-- Thread View --}}
            <div class="flex-1 flex flex-col bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-xl shadow-sm overflow-hidden">
                @php $active = $this->getActiveConversation(); @endphp

                @if(! $active)
                    <div class="flex-1 flex flex-col items-center justify-center text-zinc-400 dark:text-zinc-500 gap-3">
                        <x-heroicon-o-chat-bubble-left-right class="w-10 h-10 text-zinc-300 dark:text-zinc-700" />
                        <span class="text-sm">اختر محادثة من القائمة</span>
                    </div>
                @else
                    {{-- Thread Header --}}
                    <div class="px-5 py-4 border-b border-zinc-200 dark:border-zinc-700 flex items-center gap-3 bg-white dark:bg-zinc-900 shadow-sm z-10">
                        <div class="w-9 h-9 rounded-full bg-indigo-100 dark:bg-indigo-900 flex items-center justify-center text-indigo-600 dark:text-indigo-300 font-bold text-sm flex-shrink-0">
                            {{ mb_substr($active->guardian->name, 0, 1) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-bold text-sm text-zinc-900 dark:text-white truncate">{{ $active->guardian->name }}</p>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400 truncate">بخصوص: {{ $active->student->name }}</p>
                        </div>
                    </div>

                    {{-- Messages --}}
                    <div class="flex-1 overflow-y-auto p-5 space-y-3 bg-zinc-50/50 dark:bg-zinc-900/50" id="thread-messages">
                        @forelse($active->messages as $msg)
                            <div class="flex {{ $msg->isFromTeacher() ? 'justify-end' : 'justify-start' }}">
                                <div class="max-w-[70%] px-4 py-2 rounded-2xl text-sm leading-relaxed
                                    {{ $msg->isFromTeacher()
                                        ? 'bg-indigo-600 text-white rounded-bl-none shadow-sm'
                                        : 'bg-white dark:bg-zinc-800 text-zinc-800 dark:text-zinc-100 rounded-br-none shadow-sm border border-zinc-100 dark:border-zinc-700' }}">
                                    {{ $msg->body }}
                                    <div class="text-[10px] mt-1 opacity-60">
                                        {{ $msg->created_at->format('H:i') }}
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="flex-1 flex items-center justify-center h-full text-zinc-400 text-sm py-8">
                                ابدأ المحادثة بإرسال رسالة
                            </div>
                        @endforelse
                    </div>

                    {{-- Compose --}}
                    <div class="border-t border-zinc-200 dark:border-zinc-700 p-4 bg-white dark:bg-zinc-900 flex gap-3 items-end">
                        <textarea
                            wire:model.live="newMessage"
                            wire:keydown.ctrl.enter="sendMessage"
                            rows="2"
                            placeholder="اكتب رسالتك هنا... (Ctrl+Enter للإرسال)"
                            class="flex-1 rounded-xl border border-zinc-300 dark:border-zinc-600 bg-zinc-50 dark:bg-zinc-800 text-zinc-900 dark:text-zinc-100 text-sm px-4 py-3 focus:outline-none focus:ring-2 focus:ring-indigo-500 resize-none transition-shadow"
                        ></textarea>
                        <button
                            wire:click="sendMessage"
                            class="px-5 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-sm font-semibold transition-colors flex items-center gap-2 shadow-sm"
                        >
                            <x-heroicon-s-paper-airplane class="w-4 h-4" />
                            إرسال
                        </button>
                    </div>
                @endif
            </div>
        </div>
    @endif

    <script>
        // Auto-scroll to the bottom of the thread whenever messages update
        document.addEventListener('livewire:navigated', scrollThread);
        document.addEventListener('livewire:update', scrollThread);
        function scrollThread() {
            const el = document.getElementById('thread-messages');
            if (el) el.scrollTop = el.scrollHeight;
        }
        scrollThread();
    </script>
</x-filament-panels::page>
