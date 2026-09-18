<x-filament-panels::page>
    <style>
        .pts-page { direction: rtl; font-family: 'IBM Plex Sans Arabic', 'Cairo', sans-serif; }
        .pts-summary-bar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
            margin-bottom: 24px;
        }
        .pts-print-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: linear-gradient(135deg, #059669, #84cc16);
            color: white;
            border: none;
            padding: 10px 28px;
            font-size: 1rem;
            font-weight: 700;
            border-radius: 10px;
            cursor: pointer;
            text-decoration: none;
            box-shadow: 0 4px 15px rgba(132, 204, 22, 0.3);
            transition: transform 0.2s, opacity 0.2s;
        }
        .pts-print-btn:hover { transform: scale(1.04); opacity: 0.95; color: white; }
        .pts-table { width: 100%; border-collapse: collapse; }
        .pts-table thead tr { border-bottom: 2px solid #e2e8f0; }
        .pts-table th {
            padding: 10px 14px;
            text-align: right;
            font-size: 0.8rem;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .pts-table tbody tr {
            border-bottom: 1px solid #f1f5f9;
            transition: background 0.15s;
        }
        .pts-table tbody tr:hover { background: #f8fafc; }
        .pts-table td { padding: 12px 14px; font-size: 0.95rem; }
        .pts-rank { font-weight: 800; color: #94a3b8; font-size: 0.9rem; }
        .pts-rank.gold   { color: #f59e0b; }
        .pts-rank.silver { color: #6b7280; }
        .pts-rank.bronze { color: #b45309; }
        .pts-name { font-weight: 700; color: #1e293b; }
        .pts-teacher { font-size: 0.8rem; color: #64748b; }
        .pts-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #059669, #65a30d);
            color: white;
            font-weight: 900;
            font-size: 1rem;
            padding: 4px 16px;
            border-radius: 9999px;
            min-width: 70px;
        }
        .pts-zero { background: #e2e8f0; color: #94a3b8; }
        .pts-total-row td { font-weight: 800; background: #f8fafc; font-size: 1rem; }
    </style>

    <div class="pts-page">
        {{-- Header bar --}}
        <div class="pts-summary-bar">
            <div>
                <p style="font-size: 0.9rem; color: #64748b; margin: 0;">
                    إجمالي الطلاب: <strong>{{ $this->students->count() }}</strong>
                    &nbsp;|&nbsp;
                    إجمالي النقاط الكلية: <strong>{{ number_format($this->students->sum('total_pts')) }}</strong>
                </p>
            </div>
            <a href="{{ $this->getPrintUrl() }}" target="_blank" class="pts-print-btn">
                🖨️ &nbsp; طباعة البطاقات
            </a>
        </div>

        {{-- Students table --}}
        <div style="background: white; border-radius: 14px; box-shadow: 0 1px 4px rgba(0,0,0,0.06); overflow: hidden;">
            <table class="pts-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>اسم الطالب</th>
                        @if(auth()->user()->role === 'admin')
                            <th>المعلم</th>
                        @endif
                        <th style="text-align: center;">النقاط الإجمالية</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($this->students as $index => $student)
                        @php
                            $rank = $index + 1;
                            $rankClass = match($rank) { 1 => 'gold', 2 => 'silver', 3 => 'bronze', default => '' };
                        @endphp
                        <tr>
                            <td class="pts-rank {{ $rankClass }}">
                                @if($rank === 1) 🥇
                                @elseif($rank === 2) 🥈
                                @elseif($rank === 3) 🥉
                                @else {{ $rank }}
                                @endif
                            </td>
                            <td class="pts-name">{{ $student->name }}</td>
                            @if(auth()->user()->role === 'admin')
                                <td class="pts-teacher">{{ $student->teacher?->name ?? '—' }}</td>
                            @endif
                            <td style="text-align: center;">
                                <span class="pts-badge {{ $student->total_pts === 0 ? 'pts-zero' : '' }}">
                                    {{ number_format($student->total_pts) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" style="text-align: center; padding: 40px; color: #94a3b8;">
                                لا يوجد طلاب
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-filament-panels::page>
