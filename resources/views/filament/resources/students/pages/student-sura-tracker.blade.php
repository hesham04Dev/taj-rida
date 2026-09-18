<x-filament-panels::page>
    <style>
        :root {
            --accent-lime: #32CD32;
            --card-bg: #ffffff;
            --card-border: #f1f1f1;
            --text-primary: #1a1a1a;
            --text-secondary: #717171;
            --shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.05);
            --shadow-lg: 0 10px 25px -5px rgba(50, 205, 50, 0.15);
        }

        .dark :root {
            --card-bg: #111827;
            --card-border: rgba(255, 255, 255, 0.05);
            --text-primary: #f3f4f6;
            --text-secondary: #9ca3af;
        }

        .juz-container {
            direction: rtl;
            font-family: inherit;
        }

        /* Legend */
        .legend-bar {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
            margin-bottom: 30px;
            padding: 15px;
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: 12px;
        }

        .dark .legend-bar {
            background: #111827;
            border-color: rgba(255, 255, 255, 0.05);
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 12px;
            font-weight: 600;
            color: var(--text-secondary);
        }

        .dark .legend-item { color: #f3f4f6; }

        .dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
        }

        /* Grid */
        .juz-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 16px;
        }

        /* Card */
        .juz-card {
            position: relative;
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: 16px;
            padding: 18px 14px 14px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            min-height: 115px;
            box-shadow: var(--shadow-sm);
            overflow: hidden;
        }

        .dark .juz-card {
            background: #111827;
            border-color: rgba(255, 255, 255, 0.05);
        }

        .dark .juz-card .juz-name { color: #f3f4f6; }

        .juz-card:hover {
            transform: translateY(-4px);
            border-color: var(--accent-lime);
            box-shadow: var(--shadow-lg);
        }

        .juz-number {
            position: absolute;
            top: 10px;
            right: 12px;
            font-size: 10px;
            font-weight: 700;
            color: var(--text-secondary);
            opacity: 0.5;
        }

        .juz-name {
            font-size: 13px;
            font-weight: 800;
            color: var(--text-primary);
            margin: 10px 0 4px;
            transition: color 0.3s ease;
            line-height: 1.4;
        }

        .juz-card:hover .juz-name { color: var(--accent-lime); }

        .juz-children-count {
            font-size: 11px;
            color: var(--text-secondary);
            background: rgba(0, 0, 0, 0.03);
            padding: 2px 8px;
            border-radius: 20px;
            align-self: center;
        }

        .dark .juz-children-count { background: rgba(255, 255, 255, 0.05); }

        /* Progress bar */
        .juz-progress-bar {
            position: absolute;
            bottom: 0;
            left: 0;
            height: 4px;
            width: 100%;
            background: rgba(0,0,0,0.07);
            border-radius: 0 0 16px 16px;
            overflow: hidden;
        }

        .juz-progress-fill {
            height: 100%;
            border-radius: 0 0 16px 16px;
            transition: width 0.4s ease;
        }

        .juz-percent {
            font-size: 11px;
            font-weight: 700;
            color: var(--accent-lime);
            margin: 2px 0;
        }

        .juz-reps {
            display: flex;
            justify-content: center;
            gap: 8px;
            font-size: 11px;
            color: var(--text-secondary);
            margin-top: 2px;
        }

        /* Checkbox */
        .juz-checkbox-container {
            position: absolute;
            top: 12px;
            right: 12px;
            z-index: 10;
        }

        .juz-checkbox {
            height: 16px;
            width: 16px;
            border-radius: 4px;
            border: 1px solid #d1d5db;
            accent-color: var(--accent-lime);
            cursor: pointer;
        }

        /* Badges */
        .juz-tested-icon {
            position: absolute;
            top: 8px;
            left: 8px;
            z-index: 10;
            color: var(--accent-lime);
            width: 20px;
            height: 20px;
        }

        .juz-rememorize-badge {
            position: absolute;
            top: 8px;
            left: 8px;
            z-index: 10;
            background-color: #f97316;
            color: white;
            font-size: 9px;
            font-weight: 700;
            padding: 2px 6px;
            border-radius: 9999px;
            animation: pulse-orange 1.8s ease-in-out infinite;
        }

        @keyframes pulse-orange {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.75; transform: scale(1.08); }
        }

        .juz-revision-badge {
            position: absolute;
            top: 8px;
            left: 8px;
            z-index: 10;
            background-color: #8b5cf6;
            color: white;
            font-size: 9px;
            font-weight: 700;
            padding: 2px 6px;
            border-radius: 9999px;
            animation: pulse-purple 1.8s ease-in-out infinite;
        }

        @keyframes pulse-purple {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.75; transform: scale(1.08); }
        }

        .juz-card-body {
            display: flex;
            flex-direction: column;
            height: 100%;
            justify-content: space-between;
            padding-top: 8px;
        }

        /* Floating bulk action button */
        .floating-action-btn-container {
            position: fixed;
            bottom: 32px;
            left: 32px;
            z-index: 50;
        }

        .floating-action-btn {
            background-color: var(--accent-lime);
            color: white;
            font-weight: 700;
            font-size: 16px;
            padding: 12px 24px;
            border-radius: 9999px;
            box-shadow: 0 10px 15px -3px rgba(50, 205, 50, 0.3), 0 4px 6px -2px rgba(50, 205, 50, 0.15);
            display: flex;
            align-items: center;
            gap: 8px;
            border: none;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .floating-action-btn:hover {
            background-color: #28a428;
            transform: scale(1.05);
        }

        .floating-action-icon {
            width: 24px;
            height: 24px;
        }
    </style>

    <div class="juz-container">
        {{-- Legend --}}
        <div class="legend-bar">
            @php
                $legend = [
                    ['c' => '#94a3b8', 'l' => 'لم يبدأ'],
                    ['c' => '#38bdf8', 'l' => 'حفظ ممتاز'],
                    ['c' => '#6366f1', 'l' => 'حفظ جيد'],
                    ['c' => '#fbbf24', 'l' => 'حفظ ضعيف'],
                    ['c' => '#32CD32', 'l' => 'مراجعة ممتازة'],
                    ['c' => '#059669', 'l' => 'مراجعة جيدة'],
                ];
            @endphp
            @foreach($legend as $item)
                <div class="legend-item">
                    <span class="dot" style="background-color: {{ $item['c'] }}"></span>
                    {{ $item['l'] }}
                </div>
            @endforeach
        </div>

        {{-- Juz Grid --}}
        <div class="juz-grid">
            @foreach($this->curriculum as $juz)
                @php
                    $color = match (true) {
                        str_contains($juz->status_color, 'lime')       => '#32CD32',
                        str_contains($juz->status_color, 'light_blue') => '#38bdf8',
                        str_contains($juz->status_color, 'blue')       => '#6366f1',
                        str_contains($juz->status_color, 'yellow')     => '#fbbf24',
                        str_contains($juz->status_color, 'dark_green') => '#059669',
                        default                                         => '#94a3b8',
                    };
                    $percent = $juz->memorization_percent;
                @endphp

                <div class="juz-card">
                    {{-- Multi-select checkbox --}}
                    <div class="juz-checkbox-container" wire:click.stop>
                        <input type="checkbox"
                               wire:model.live="selectedJuz"
                               value="{{ $juz->id }}"
                               class="juz-checkbox">
                    </div>

                    {{-- Status badges --}}
                    @if($juz->is_tested)
                        <div class="juz-tested-icon" title="تم اختباره">
                            <x-heroicon-s-check-circle />
                        </div>
                    @elseif($juz->is_need_rememorisation)
                        <div class="juz-rememorize-badge" title="يحتاج إعادة حفظ">إعادة</div>
                    @elseif($juz->is_need_revision)
                        <div class="juz-revision-badge" title="يحتاج مراجعة">مراجعة</div>
                    @endif

                    {{-- Card body — clicking opens the tasmee dialog --}}
                    <div wire:click="mountAction('addLog', { juz: {{ $juz->id }} })" class="juz-card-body">
                        <span class="juz-number">{{ $juz->number }}</span>

                        <div class="juz-name">{{ $juz->name }}</div>

                        <div class="juz-children-count">
                            {{ $juz->children_count }} {{ $juz->number === 30 ? 'سورة' : 'صفحة' }}
                        </div>

                        @if($percent > 0 && $percent < 100)
                            <div class="juz-percent">{{ $percent }}%</div>
                        @endif

                        @if($juz->memorization_repetition > 0 || $juz->revision_repetition > 0)
                            <div class="juz-reps" dir="rtl">
                                @if($juz->memorization_repetition > 0)
                                    <span title="مرات الحفظ">⟳{{ $juz->memorization_repetition }}</span>
                                @endif
                                @if($juz->revision_repetition > 0)
                                    <span title="مرات المراجعة" style="color: #32CD32">↺{{ $juz->revision_repetition }}</span>
                                @endif
                            </div>
                        @endif
                    </div>

                    {{-- Progress bar --}}
                    <div class="juz-progress-bar">
                        <div class="juz-progress-fill"
                             style="width: {{ $percent }}%; background-color: {{ $color }};"></div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    @if(count($selectedJuz) > 0)
        <div class="floating-action-btn-container">
            <button wire:click="mountAction('bulkAddLog')" class="floating-action-btn">
                <x-heroicon-o-check-circle class="floating-action-icon" />
                تسجيل إنجاز ({{ count($selectedJuz) }})
            </button>
        </div>
    @endif

    <x-filament-actions::modals />
</x-filament-panels::page>