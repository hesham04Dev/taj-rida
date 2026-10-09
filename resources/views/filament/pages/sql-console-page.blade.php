<x-filament-panels::page>
    @if (!$this->confirmed)
        {{-- ══════════════════════════════════════════════════════════
             Password Confirmation Gate
        ══════════════════════════════════════════════════════════ --}}
        <div class="max-w-md mx-auto mt-10">
            <div class="bg-gray-900 border border-gray-700 rounded-2xl shadow-2xl p-8 text-center"
                 x-data="{ password: '' }">
                <div class="mb-6">
                    <x-heroicon-o-lock-closed class="w-16 h-16 text-amber-400 mx-auto mb-3" />
                    <h2 class="text-2xl font-bold text-white mb-2">وحدة تحكم SQL</h2>
                    <p class="text-gray-400 text-sm">هذه منطقة محمية. يرجى تأكيد كلمة مرورك للمتابعة.</p>
                </div>

                <div class="space-y-4">
                    <input
                        type="password"
                        x-model="password"
                        @keydown.enter="$wire.confirmPassword(password)"
                        placeholder="كلمة المرور"
                        class="w-full bg-gray-800 border border-gray-600 rounded-lg px-4 py-3 text-white placeholder-gray-500 focus:outline-none focus:border-amber-500 focus:ring-1 focus:ring-amber-500 text-center"
                        autocomplete="current-password"
                    />
                    <button
                        @click="$wire.confirmPassword(password)"
                        class="w-full bg-amber-500 hover:bg-amber-400 text-gray-900 font-bold py-3 px-6 rounded-lg transition-colors duration-200"
                    >
                        تأكيد والدخول
                    </button>
                </div>

                <p class="mt-4 text-xs text-gray-600">
                    يُعاد طلب كلمة المرور كل 10 دقائق.
                </p>
            </div>
        </div>
    @else
        {{-- ══════════════════════════════════════════════════════════
             SQL Console
        ══════════════════════════════════════════════════════════ --}}

        {{-- Status Bar --}}
        <div class="flex items-center justify-between mb-4 px-1">
            <div class="flex items-center gap-3">
                <span class="inline-flex items-center gap-1.5 text-xs font-medium px-2.5 py-1 rounded-full
                    {{ $this->isWriteModeEnabled ? 'bg-red-900/60 text-red-300 border border-red-700' : 'bg-green-900/60 text-green-300 border border-green-700' }}">
                    <span class="w-1.5 h-1.5 rounded-full {{ $this->isWriteModeEnabled ? 'bg-red-400' : 'bg-green-400' }} animate-pulse"></span>
                    {{ $this->isWriteModeEnabled ? 'وضع الكتابة (Write Mode)' : 'وضع القراءة فقط (Read-Only)' }}
                </span>
                <span class="text-xs text-gray-500">
                    ⚡ الحد: 30 استعلام / دقيقة
                </span>
            </div>
            <div class="flex items-center gap-2">
                @if ($results !== null || $errorMessage || $successMessage)
                    <button
                        wire:click="clearResults"
                        class="text-xs text-gray-400 hover:text-white flex items-center gap-1 transition-colors"
                    >
                        <x-heroicon-o-x-mark class="w-3.5 h-3.5" /> مسح
                    </button>
                @endif
                <span class="text-xs text-gray-600">
                    {{ now()->format('H:i') }} — جلسة مؤمّنة لـ 10 دقائق
                </span>
            </div>
        </div>

        {{-- Editor Area --}}
        <div class="bg-gray-950 border border-gray-800 rounded-2xl overflow-hidden shadow-2xl mb-4">
            {{-- Top Bar --}}
            <div class="flex items-center justify-between px-4 py-2.5 bg-gray-900 border-b border-gray-800">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-red-500"></span>
                    <span class="w-3 h-3 rounded-full bg-yellow-500"></span>
                    <span class="w-3 h-3 rounded-full bg-green-500"></span>
                </div>
                <span class="text-xs text-gray-500 font-mono">SQL Console — {{ config('app.name') }}</span>
                <x-heroicon-o-command-line class="w-4 h-4 text-gray-600" />
            </div>

            {{-- Textarea --}}
            <div class="relative">
                <textarea
                    wire:model="sql"
                    rows="8"
                    placeholder="{{ $this->isWriteModeEnabled ? 'أدخل استعلام SQL هنا...' : 'أدخل استعلام SELECT / SHOW / DESCRIBE / EXPLAIN هنا...' }}"
                    class="w-full bg-gray-950 text-green-300 font-mono text-sm p-5 resize-y focus:outline-none placeholder-gray-700 leading-relaxed"
                    dir="ltr"
                    spellcheck="false"
                ></textarea>
            </div>

            {{-- Footer --}}
            <div class="flex items-center justify-between px-4 py-3 bg-gray-900 border-t border-gray-800">
                <div class="text-xs text-gray-600 font-mono">
                    @if($this->isWriteModeEnabled)
                        ✅ SELECT, SHOW, DESCRIBE, EXPLAIN, INSERT, UPDATE, DELETE, CREATE, DROP, ALTER, TRUNCATE
                    @else
                        🔒 SELECT, SHOW, DESCRIBE, EXPLAIN فقط
                    @endif
                </div>
                <button
                    wire:click="executeQuery"
                    wire:loading.attr="disabled"
                    class="inline-flex items-center gap-2 bg-teal-600 hover:bg-teal-500 disabled:opacity-50 text-white text-sm font-semibold px-5 py-2 rounded-lg transition-colors duration-200"
                >
                    <x-heroicon-o-play class="w-4 h-4" />
                    <span wire:loading.remove wire:target="executeQuery">تشغيل</span>
                    <span wire:loading wire:target="executeQuery">جارٍ التنفيذ...</span>
                </button>
            </div>
        </div>

        {{-- Results / Errors --}}
        @if ($errorMessage)
            <div class="bg-red-950/60 border border-red-800 rounded-xl p-4 mb-4 flex items-start gap-3">
                <x-heroicon-o-exclamation-triangle class="w-5 h-5 text-red-400 mt-0.5 shrink-0" />
                <p class="text-red-300 font-mono text-sm">{{ $errorMessage }}</p>
            </div>
        @endif

        @if ($successMessage)
            <div class="bg-green-950/60 border border-green-800 rounded-xl p-4 mb-4 flex items-start gap-3">
                <x-heroicon-o-check-circle class="w-5 h-5 text-green-400 mt-0.5 shrink-0" />
                <p class="text-green-300 font-mono text-sm">{{ $successMessage }}</p>
            </div>
        @endif

        @if ($results !== null)
            <div class="bg-gray-950 border border-gray-800 rounded-2xl overflow-hidden shadow-xl">
                {{-- Results Header --}}
                <div class="flex items-center justify-between px-4 py-2.5 bg-gray-900 border-b border-gray-800">
                    <span class="text-xs font-semibold text-gray-400">النتائج</span>
                    <div class="flex items-center gap-4 text-xs text-gray-500">
                        <span>{{ $rowCount }} صف</span>
                        <span>{{ $executionTime }}ms</span>
                    </div>
                </div>

                @if (count($results) > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full text-xs font-mono">
                            <thead>
                                <tr class="border-b border-gray-800">
                                    @foreach (array_keys($results[0]) as $col)
                                        <th class="px-4 py-2 text-left text-teal-400 font-semibold bg-gray-900/50 whitespace-nowrap">
                                            {{ $col }}
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($results as $i => $row)
                                    <tr class="border-b border-gray-900 {{ $i % 2 === 0 ? 'bg-gray-950' : 'bg-gray-900/30' }} hover:bg-gray-800/40 transition-colors">
                                        @foreach ($row as $cell)
                                            <td class="px-4 py-2 text-gray-300 whitespace-nowrap max-w-xs truncate"
                                                title="{{ is_null($cell) ? 'NULL' : $cell }}">
                                                @if (is_null($cell))
                                                    <span class="text-gray-600 italic">NULL</span>
                                                @else
                                                    {{ $cell }}
                                                @endif
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="flex items-center justify-center py-12 text-gray-600">
                        <div class="text-center">
                            <x-heroicon-o-inbox class="w-10 h-10 mx-auto mb-2" />
                            <p class="text-sm">لا توجد نتائج</p>
                        </div>
                    </div>
                @endif
            </div>
        @endif

        {{-- Hint footer --}}
        <div class="mt-6 text-center text-xs text-gray-700">
            ⚠️ جميع الاستعلامات مسجّلة. هذه الصفحة مخصصة للمسؤول الأعلى فقط.
        </div>
    @endif
</x-filament-panels::page>
