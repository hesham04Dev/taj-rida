<x-filament-panels::page>
    <style>
        :root {
            --accent-lime: #32CD32;
            --card-bg: #ffffff;
            --card-border: #f1f1f1;
            --text-primary: #1a1a1a;
            --text-secondary: #717171;
            --shadow-sm: 0 1px 3px rgba(0,0,0,0.05);
            --shadow-lg: 0 10px 25px -5px rgba(50, 205, 50, 0.15);
        }

        /* Dark Mode overrides for Filament */
        .dark :root {
            --card-bg: #111827;
            --card-border: rgba(255, 255, 255, 0.05);
            --text-primary: #f3f4f6;
            --text-secondary: #9ca3af;
        }

        .sura-container {
            direction: rtl;
            font-family: inherit;
        }

        /* Legend Styling */
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

        .legend-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 12px;
            font-weight: 600;
            color: var(--text-secondary);
        }

        .dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
        }

        /* Grid Layout */
        .sura-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(130px, 1fr));
            gap: 16px;
        }

        /* Card Styling */
        .sura-card {
            position: relative;
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: 16px;
            padding: 20px 15px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            min-height: 110px;
            box-shadow: var(--shadow-sm);
            overflow: hidden;
        }

        .sura-card:hover {
            transform: translateY(-4px);
            border-color: var(--accent-lime);
            box-shadow: var(--shadow-lg);
        }

        .sura-id {
            position: absolute;
            top: 10px;
            right: 12px;
            font-size: 10px;
            font-weight: 700;
            color: var(--text-secondary);
            opacity: 0.5;
        }

        .sura-name {
            font-size: 18px;
            font-weight: 800;
            color: var(--text-primary);
            margin: 10px 0;
            transition: color 0.3s ease;
        }

        .sura-card:hover .sura-name {
            color: var(--accent-lime);
        }

        .sura-ayas {
            font-size: 11px;
            color: var(--text-secondary);
            background: rgba(0,0,0,0.03);
            padding: 2px 8px;
            border-radius: 20px;
            align-self: center;
        }

        .dark .sura-ayas {
            background: rgba(255,255,255,0.05);
        }

        /* Status Line Indicator */
        .status-indicator {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 4px;
            opacity: 0.8;
        }

        .sura-percent {
            font-size: 12px;
            font-weight: 700;
            color: var(--accent-lime);
            margin: 2px 0;
        }

        .sura-reps {
            display: flex;
            justify-content: center;
            gap: 8px;
            font-size: 11px;
            color: var(--text-secondary);
            margin-top: 2px;
        }
    </style>

    <div class="sura-container">
        {{-- Legend --}}
        <div class="legend-bar">
            @php
                $legend = [
                    ['c' => '#94a3b8', 'l' => 'لم تحفظ'],
                    ['c' => '#38bdf8', 'l' => 'ممتاز'],
                    ['c' => '#6366f1', 'l' => 'جيد'],
                    ['c' => '#fbbf24', 'l' => 'ضعيف'],
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

        {{-- Grid --}}
        <div class="sura-grid">
            @foreach($this->suras as $sura)
                @php
                    $color = match(true) {
                        str_contains($sura->status_color, 'lime') => '#32CD32',
                        str_contains($sura->status_color, 'light_blue') => '#38bdf8',
                        str_contains($sura->status_color, 'blue') => '#6366f1',
                        str_contains($sura->status_color, 'yellow') => '#fbbf24',
                        str_contains($sura->status_color, 'dark_green') => '#059669',
                        default => '#94a3b8',
                    };
                @endphp

                <div class="sura-card" wire:click="mountAction('addLog', { sura: {{ $sura->id }} })">
                    <span class="sura-id">{{ $sura->id }}</span>
                    
                    <div class="sura-name">{{ $sura->name }}</div>
                    
                    <div class="sura-ayas">{{ $sura->ayas_count }} آية</div>

                    @if($sura->memorization_percent > 0 && $sura->memorization_percent < 100)
                        <div class="sura-percent">{{ $sura->memorization_percent }}%</div>
                    @endif

                    @if($sura->memorization_repetition > 0 || $sura->revision_repetition > 0)
                        <div class="sura-reps" dir="rtl">
                            @if($sura->memorization_repetition > 0)
                                <span title="مرات الحفظ">⟳{{ $sura->memorization_repetition }}</span>
                            @endif
                            @if($sura->revision_repetition > 0)
                                <span title="مرات المراجعة" style="color: #32CD32">↺{{ $sura->revision_repetition }}</span>
                            @endif
                        </div>
                    @endif
                    
                    {{-- Visual indicator --}}
                    <div class="status-indicator" style="background-color: {{ $color }}"></div>
                </div>
            @endforeach
        </div>
    </div>

    <x-filament-actions::modals />
</x-filament-panels::page>