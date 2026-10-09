<?php

namespace App\Filament\Resources\BudgetTransactions\Pages;

use App\Filament\Resources\BudgetTransactions\BudgetTransactionResource;
use App\Filament\Resources\BudgetTransactions\Widgets\BudgetStats;
use App\Filament\Widgets\GiftsAndExchangeRatesWidget;
use App\Models\Setting;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListBudgetTransactions extends ListRecords
{
    protected static string $resource = BudgetTransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('manageGiftsAndRates')
                ->label('رصيد الهدايا وسعر الصرف')
                ->icon('heroicon-o-banknotes')
                ->color('warning')
                ->form([
                    TextInput::make('gifts_balance')
                        ->label('رصيد الهدايا المخصص')
                        ->helperText('المبلغ المالي المخصص لرصيد الهدايا لحساب سعر الصرف')
                        ->numeric()
                        ->minValue(0)
                        ->required(),
                    TextInput::make('expected_exchange_rate')
                        ->label('سعر الصرف المتوقع للنقاط')
                        ->helperText('القيمة النقدية المقابلة للنقطة الواحدة (مثال: 0.1)')
                        ->numeric()
                        ->minValue(0.0001)
                        ->required(),
                ])
                ->fillForm(function (): array {
                    $giftsSetting = Setting::where('key', 'gifts_balance')->first();
                    $expectedRateSetting = Setting::where('key', 'expected_exchange_rate')->first();

                    return [
                        'gifts_balance' => $giftsSetting ? (float) $giftsSetting->value : 0,
                        'expected_exchange_rate' => $expectedRateSetting ? (float) $expectedRateSetting->value : 0.1,
                    ];
                })
                ->action(function (array $data): void {
                    Setting::updateOrCreate(['key' => 'gifts_balance'], ['value' => (string) $data['gifts_balance']]);
                    Setting::updateOrCreate(['key' => 'expected_exchange_rate'], ['value' => (string) $data['expected_exchange_rate']]);

                    Notification::make()
                        ->title('تم حفظ رصيد الهدايا وسعر الصرف بنجاح')
                        ->success()
                        ->send();
                }),
            CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            BudgetStats::class,
            GiftsAndExchangeRatesWidget::class,
        ];
    }
}
