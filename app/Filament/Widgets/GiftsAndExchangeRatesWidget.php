<?php

namespace App\Filament\Widgets;

use App\Models\BudgetTransaction;
use App\Models\Creditor;
use App\Models\PointTransaction;
use App\Models\Setting;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class GiftsAndExchangeRatesWidget extends BaseWidget
{
    protected static ?int $sort = 0;

    public static function canView(): bool
    {
        return auth()->user()?->role === 'admin';
    }

    protected function getStats(): array
    {
        // 1. Gifts Balance
        $giftsSetting = Setting::where('key', 'gifts_balance')->first();
        if ($giftsSetting && is_numeric($giftsSetting->value) && (float) $giftsSetting->value > 0) {
            $giftsBalance = (float) $giftsSetting->value;
        } else {
            $giftsSum = (float) BudgetTransaction::where('type', 'gift')->sum('amount');
            if ($giftsSum > 0) {
                $giftsBalance = $giftsSum;
            } else {
                $creditors = Creditor::with('budgetTransactions')->get();
                $giftsBalance = (float) $creditors->sum('remaining_balance');
            }
        }

        // 2. Total Points (for the current active Dawara, since PointTransaction has a global scope)
        $totalPoints = (float) PointTransaction::sum('amount');

        // 3. Actual Exchange Rate
        $actualRate = 0.0;
        if ($totalPoints > 0) {
            $actualRate = $giftsBalance / $totalPoints;
        }

        // 4. Expected Exchange Rate
        $expectedRateSetting = Setting::where('key', 'expected_exchange_rate')->first();
        $expectedRate = $expectedRateSetting ? (float) $expectedRateSetting->value : 0;

        return [
            Stat::make('رصيد الهدايا (Gifts Balance)', number_format($giftsBalance, 2))
                ->description('ملاحظة: غير مرتبط بالرصيد الحالي')
                ->descriptionIcon('heroicon-m-bell')
                ->color('warning'),

            Stat::make('سعر الصرف الفعلي', number_format($actualRate, 4).' / نقطة')
                ->description('1 نقطة = '.number_format($actualRate, 4))
                ->descriptionIcon('heroicon-m-calculator')
                ->color('info'),

            Stat::make('سعر الصرف المتوقع', number_format($expectedRate, 4).' / نقطة')
                ->description('السعر المحدد من الإدارة')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color('warning'),
        ];
    }
}
