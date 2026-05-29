<?php

namespace App\Filament\Resources\Students\Tables;

use App\Models\Attendance;
use App\Models\PointTransaction;
use App\Models\Setting;
use App\Models\Student;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleButtons;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class StudentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->contentGrid([
                'md' => 2,
                'xl' => 2,
                'sm' => 1,
            ])
            ->columns([
                Stack::make([
                    Split::make([
                        TextColumn::make('index')
                            ->label('#')
                            ->getStateUsing(static function ($record, $livewire) {
                                // جلب السجلات الحالية للصفحة
                                $records = $livewire->getTableRecords();

                                // تحويل السجلات لمصفوفة سواء كانت مقسمة لصفحات (Paginated) أو مصفوفة عادية
                                $items = method_exists($records, 'items') ? $records->items() : $records->all();

                                // البحث عن مكان السجل الحالي بمقارنة الـ ID لمنع مشاكل التطابق
                                $index = collect($items)->search(fn ($item) => $item->getKey() === $record->getKey());

                                if ($index === false) {
                                    return null;
                                }

                                // إذا كان هناك تقسيم صفحات (Pagination)، يتم حساب الترتيب التراكمي
                                return method_exists($records, 'firstItem')
                                    ? $records->firstItem() + $index
                                    : $index + 1;
                            })->grow(false),
                        TextColumn::make('name')
                            ->label('اسم الطالب')
                            ->weight('bold')
                            ->size('large')
                            ->searchable()
                            ->sortable(),
                    ]),

                    TextColumn::make('teacher.name')
                        ->label('الأستاذ')
                        ->color('gray')
                        ->size('small')
                        ->icon('heroicon-m-user')
                        ->sortable()
                        ->toggleable(isToggledHiddenByDefault: auth()->check() && auth()->user()->role === 'teacher'),

                    Split::make([
                        TextColumn::make('points_multiplier')
                            ->label('مضاعف')
                            ->formatStateUsing(fn ($state) => 'مضاعف: '.$state)
                            ->badge()
                            ->color('info')
                            ->grow(false),

                        TextColumn::make('total_points')
                            ->label('النقاط')
                            ->getStateUsing(fn ($record) => $record->pointTransactions()->sum('amount'))
                            ->icon('heroicon-m-star')
                            ->badge()
                            ->color('warning')
                            ->sortable(query: function ($query, string $direction) {
                                $query->addSelect([
                                    'total_points_sum' => PointTransaction::selectRaw('sum(amount)')
                                        ->whereColumn('student_id', 'students.id'), // Ensure 'students.id' matches your table name
                                ])
                                    ->orderBy('total_points_sum', $direction);
                            }),

                        TextColumn::make('given_points')
                            ->label('موزعة')
                            ->formatStateUsing(fn ($state) => 'موزع: '.$state)
                            ->badge()
                            ->color('success')
                            ->sortable(false),
                    ]),
                ])->space(3),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                Action::make('attendance')
                    ->label(fn ($record) => $record->attendances()->whereDate('date', Carbon::today())->where('is_present', true)->exists() ? 'حاضر' : 'غائب')
                    ->color(fn ($record) => $record->attendances()->whereDate('date', Carbon::today())->where('is_present', true)->exists() ? 'success' : 'danger')
                    ->icon('heroicon-o-check-circle')
                    ->action(function ($record) {
                        $attendance = Attendance::firstOrNew([
                            'student_id' => $record->id,
                            'date' => Carbon::today(),
                        ]);
                        $attendance->is_present = ! $attendance->exists || ! $attendance->is_present;
                        $attendance->save();
                    }),
                Action::make('grant_points')
                    ->label('منح نقاط')
                    ->icon('heroicon-o-gift')
                    ->color('success') // Use that Lime Green feel
                    ->modalHeading('إضافة أو خصم نقاط')
                    ->modalWidth('md')
                    ->form(function () {
                        $reasonsSetting = Setting::where('key', 'point_reasons')->first();
                        $predefinedReasons = $reasonsSetting ? json_decode($reasonsSetting->value, true) : [
                            ['reason' => 'مكافأة', 'amount' => 10, 'type' => 'addition'],
                            ['reason' => 'مشاركة', 'amount' => 15, 'type' => 'addition'],
                            ['reason' => 'التزام', 'amount' => 20, 'type' => 'addition'],
                            ['reason' => 'خصم سلوك', 'amount' => 10, 'type' => 'deduction'],
                           
                        ];

                        $reasonOptions = [];
                        foreach ($predefinedReasons as $item) {
                            $symbol = $item['type'] === 'deduction' ? '-' : '+';
                            $reasonOptions[$item['reason']] = $item['reason'].' ('.$symbol.$item['amount'].')';
                        }
                        $reasonOptions['custom'] = 'سبب آخر';

                        $defaultReason = array_key_first($reasonOptions) ?: 'custom';
                        $defaultAmount = 10;
                        $defaultDeduction = false;
                        if ($defaultReason !== 'custom') {
                            $match = collect($predefinedReasons)->firstWhere('reason', $defaultReason);
                            if ($match) {
                                $defaultAmount = $match['amount'];
                                $defaultDeduction = $match['type'] === 'deduction';
                            }
                        }

                        return [
                            Grid::make(2)->schema([
                                // 1. Quick Reason Select
                                Select::make('quick_reason')
                                    ->label('السبب')
                                    ->options($reasonOptions)
                                    ->default($defaultReason)
                                    ->columnSpanFull()
                                    ->live()
                                    ->afterStateUpdated(function ($state, callable $set) use ($predefinedReasons) {
                                        if ($state !== 'custom') {
                                            $match = collect($predefinedReasons)->firstWhere('reason', $state);
                                            if ($match) {
                                                $set('is_deduction', $match['type'] === 'deduction');
                                                $set('amount', $match['amount']);
                                            }
                                        } else {
                                            $set('amount', null);
                                        }
                                    }),

                                // 2. Custom Reason (Visible only if 'custom' is selected)
                                TextInput::make('custom_reason')
                                    ->label('اكتب السبب')
                                    ->hidden(fn (Get $get) => $get('quick_reason') !== 'custom')
                                    ->required(fn (Get $get) => $get('quick_reason') === 'custom')
                                    ->columnSpanFull(),

                                // 3. Points amount
                                TextInput::make('amount')
                                    ->label('النقاط')
                                    ->numeric()
                                    ->required()
                                    ->default($defaultAmount)
                                    ->columnSpanFull(),

                                // 4. Toggle for Adding/Removing
                                Toggle::make('is_deduction')
                                    ->label('خصم النقاط')
                                    ->onColor('danger')
                                    ->offColor('success')
                                    ->onIcon('heroicon-m-minus-circle')
                                    ->offIcon('heroicon-m-plus-circle')
                                    ->default($defaultDeduction)
                                    ->columnSpanFull(),
                            ]),
                        ];
                    })
                    ->action(function ($record, array $data) {
                        $finalAmount = (int) $data['amount'];
                        if ($data['is_deduction']) {
                            $finalAmount = -abs($finalAmount);
                        }

                        $finalReason = $data['quick_reason'] === 'custom'
                            ? $data['custom_reason']
                            : $data['quick_reason'];

                        PointTransaction::create([
                            'student_id' => $record->id,
                            'teacher_id' => auth()->id() ?? 1,
                            'amount' => $finalAmount,
                            'reason' => $finalReason,
                        ]);
                    })
                    ->successNotificationTitle('تمت العملية بنجاح'),

                Action::make('give_points')
                    ->label('توزيع كرت')
                    ->icon('heroicon-o-ticket')
                    ->color('warning')
                    ->modalHeading(fn ($record) => 'توزيع كرت - '.$record->name)
                    ->modalWidth('sm')
                    ->fillForm(function ($record): array {
                        return [
                            'card_value' => (string) $record->suggested_card_value,
                        ];
                    })
                    ->form(function ($record) {
                        $remaining = $record->remaining_points;
                        $availableCards = collect(Student::CARD_DENOMINATIONS)
                            ->mapWithKeys(fn ($v) => [(string) $v => $v.' نقطة'])
                            ->all();

                        return [
                            Grid::make(1)->schema([
                                ToggleButtons::make('card_value')
                                    ->label('اختر قيمة الكرت')
                                    ->helperText('الرصيد المتاح: '.$remaining.' نقطة')
                                    ->options($availableCards)
                                    ->inline()
                                    ->required(),
                            ]),
                        ];
                    })
                    ->action(function ($record, array $data) {
                        $cardValue = (int) $data['card_value'];
                        $remaining = $record->remaining_points;

                        if ($cardValue > $remaining) {
                            Notification::make()
                                ->title('لا يمكن توزيع '.$cardValue.' نقطة - الرصيد المتاح '.$remaining.' فقط')
                                ->danger()
                                ->send();

                            return;
                        }

                        $record->increment('given_points', $cardValue);

                        Notification::make()
                            ->title('تم توزيع كرت '.$cardValue.' نقطة بنجاح')
                            ->success()
                            ->send();
                    }),
                Action::make('sura_tracker')
                    ->label('متابعة السور')
                    ->icon('heroicon-o-book-open')
                    ->color('info')
                    ->url(fn ($record) => '/app/students/'.$record->id.'/tracker'),
                EditAction::make()->iconButton(),
            ])
            ->headerActions([
                Action::make('mark_all_absent')
                    ->label('تسجيل البقية غياب')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('تأكيد تسجيل الغياب')
                    ->modalDescription('هل أنت متأكد من تسجيل الغياب وتطبيق الخصم لجميع الطلاب الذين لم يتم تسجيل حضورهم اليوم؟')
                    ->action(function () {
                        $students = Student::when(auth()->user()->role === 'teacher', function ($q) {
                            $q->where('teacher_id', auth()->id());
                        })->get();

                        foreach ($students as $student) {
                            $existing = $student->attendances()->whereDate('date', Carbon::today())->first();
                            if (! $existing) {
                                $att = Attendance::create([
                                    'student_id' => $student->id,
                                    'date' => Carbon::today(),
                                    'is_present' => false,
                                ]);
                            }
                        }
                    }),
            ])
            ->defaultSort('name');
    }
}
