<x-filament-panels::page>
    <style>
        .needs-container {
            direction: rtl;
        }

        /* Student Card */
        .student-card {
            background: var(--fi-color-white);
            border: 1px solid rgba(0, 0, 0, 0.07);
            border-radius: 16px;
            padding: 20px 24px;
            margin-bottom: 16px;
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.05);
        }

        .dark .student-card {
            background: #111827;
            border-color: rgba(255, 255, 255, 0.05);
        }

        /* Student Header */
        .student-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 18px;
        }

        .student-avatar {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background: linear-gradient(135deg, #14b8a6, #0d9488);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 17px;
            flex-shrink: 0;
        }

        .student-name {
            font-size: 17px;
            font-weight: 700;
            color: #111827;
            flex: 1;
        }

        .dark .student-name {
            color: #f3f4f6;
        }

        .tracker-link {
            text-decoration: none;
            color: #0ea5e9;
            font-size: 13px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 4px;
            opacity: 0.85;
            transition: opacity 0.2s;
        }

        .tracker-link:hover {
            opacity: 1;
        }

        /* Section rows */
        .needs-section {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 10px 0;
        }

        .needs-section+.needs-section {
            border-top: 1px solid rgba(0, 0, 0, 0.05);
        }

        .dark .needs-section+.needs-section {
            border-top-color: rgba(255, 255, 255, 0.05);
        }

        .section-label {
            font-size: 12px;
            font-weight: 700;
            padding: 4px 10px;
            border-radius: 9999px;
            white-space: nowrap;
            flex-shrink: 0;
            margin-top: 2px;
        }

        .label-orange {
            background: #fff7ed;
            color: #c2410c;
            border: 1px solid #fed7aa;
        }

        .label-purple {
            background: #f5f3ff;
            color: #6d28d9;
            border: 1px solid #ddd6fe;
        }

        .dark .label-orange {
            background: rgba(249, 115, 22, 0.12);
            color: #fb923c;
            border-color: rgba(249, 115, 22, 0.3);
        }

        .dark .label-purple {
            background: rgba(139, 92, 246, 0.12);
            color: #a78bfa;
            border-color: rgba(139, 92, 246, 0.3);
        }

        /* Chips */
        .chips-wrap {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
        }

        .chip {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 13px;
            font-weight: 600;
            padding: 4px 12px;
            border-radius: 9999px;
        }

        .chip-orange {
            background: #fff7ed;
            border: 1.5px solid #f97316;
            color: #c2410c;
        }

        .chip-purple {
            background: #f5f3ff;
            border: 1.5px solid #8b5cf6;
            color: #6d28d9;
        }

        .dark .chip-orange {
            background: rgba(249, 115, 22, 0.1);
            border-color: rgba(249, 115, 22, 0.5);
            color: #fb923c;
        }

        .dark .chip-purple {
            background: rgba(139, 92, 246, 0.1);
            border-color: rgba(139, 92, 246, 0.5);
            color: #a78bfa;
        }

        .chip-dot {
            width: 5px;
            height: 5px;
            border-radius: 50%;
            flex-shrink: 0;
            animation: pulse-dot 1.8s ease-in-out infinite;
        }

        .dot-orange {
            background: #f97316;
        }

        .dot-purple {
            background: #8b5cf6;
        }

        .chip-pages {
            font-size: 10px;
            font-weight: 500;
            opacity: 0.7;
        }

        @keyframes pulse-dot {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.4;
            }
        }

        /* Empty state */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #9ca3af;
        }

        .empty-state-title {
            font-size: 18px;
            font-weight: 600;
            color: #6b7280;
            margin: 16px 0 8px;
        }

        .empty-state-sub {
            font-size: 14px;
        }
    </style>

    <div class="needs-container">

        @if(count($this->groupedNeeds) === 0)
            <div class="empty-state">
                <x-heroicon-o-check-badge style="width:64px;height:64px;margin:0 auto;color:#d1d5db;" />
                <div class="empty-state-title">لا توجد سور بحاجة متابعة</div>

            </div>
        @else
            @foreach($this->groupedNeeds as $group)
                <div class="student-card">

                    {{-- Student Header --}}
                    <div class="student-header">
                        <div class="student-avatar">
                            {{ mb_substr($group['student']->name, 0, 1) }}
                        </div>
                        <div class="student-name">{{ $group['student']->name }}</div>
                        <a href="{{ route('filament.admin.resources.students.tracker', $group['student']->id) }}"
                            class="tracker-link">
                            <x-heroicon-o-arrow-top-right-on-square style="width:14px;height:14px;" />
                            متابعة السور
                        </a>
                    </div>

                    {{-- Memorization Row --}}
                    @if(count($group['memorization']) > 0)
                        <div class="needs-section">
                            <span class="section-label label-orange">حفظ</span>
                            <div class="chips-wrap">
                                @foreach($group['memorization'] as $sura)
                                    <div class="chip chip-orange">
                                        <span class="chip-dot dot-orange"></span>
                                        {{ $sura['name'] }}
                                        @if(isset($sura['need_from_page']) && isset($sura['need_to_page']))
                                            <span class="chip-pages">ص {{ $sura['need_from_page'] }} ← {{ $sura['need_to_page'] }}</span>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Revision Row --}}
                    @if(count($group['revision']) > 0)
                        <div class="needs-section">
                            <span class="section-label label-purple">مراجعة</span>
                            <div class="chips-wrap">
                                @foreach($group['revision'] as $sura)
                                    <div class="chip chip-purple">
                                        <span class="chip-dot dot-purple"></span>
                                        {{ $sura['name'] }}
                                        @if(isset($sura['need_from_page']) && isset($sura['need_to_page']))
                                            <span class="chip-pages">ص {{ $sura['need_from_page'] }} ← {{ $sura['need_to_page'] }}</span>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                </div>
            @endforeach
        @endif

    </div>




</x-filament-panels::page>