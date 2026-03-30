<?php
namespace App\Filament\Resources\Students\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\Layout\Split;
use Filament\Actions\Action;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Utilities\Get;
class StudentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->contentGrid([
                'md' => 2,
                'xl' => 2,
                'sm' => 1
            ])
            ->columns([
                Stack::make([
                    TextColumn::make('name')
                        ->label('اسم الطالب')
                        ->weight("bold")
                        ->size("large")
                        ->searchable()
                        ->sortable(),

                    TextColumn::make('teacher.name')
                        ->label('الأستاذ')
                        ->color('gray')
                        ->size("small")
                        ->icon('heroicon-m-user')
                        ->sortable()
                        ->toggleable(isToggledHiddenByDefault: auth()->check() && auth()->user()->role === 'teacher'),

                    Split::make([
                        TextColumn::make('points_multiplier')
                            ->label('مضاعف')
                            ->formatStateUsing(fn ($state) => 'مضاعف: ' . $state)
                            ->badge()
                            ->color('info')
                            ->grow(false),

                        TextColumn::make('total_points')
                            ->label('النقاط')
                            ->getStateUsing(fn ($record) => $record->pointTransactions()->sum('amount'))
                            ->icon('heroicon-m-star')
                            ->badge()
                            ->color('warning')
                            ->sortable(false),
                    ]),
                ])->space(3),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                Action::make('attendance')
                    ->label(fn ($record) => $record->attendances()->whereDate('date', \Carbon\Carbon::today())->where('is_present', true)->exists() ? 'حاضر' : 'غائب')
                    ->color(fn ($record) => $record->attendances()->whereDate('date', \Carbon\Carbon::today())->where('is_present', true)->exists() ? 'success' : 'danger')
                    ->icon('heroicon-o-check-circle')
                    ->action(function ($record) {
                        $attendance = \App\Models\Attendance::firstOrNew([
                            'student_id' => $record->id,
                            'date' => \Carbon\Carbon::today(),
                        ]);
                        $attendance->is_present = !$attendance->exists || !$attendance->is_present;
                        $attendance->save();
                    }),
                Action::make('page_log')
                    ->label('سجل الصفحات')
                    ->icon('heroicon-o-document-text')
                    ->color('warning')
                    ->form([
                        \Filament\Forms\Components\ToggleButtons::make('type')
                            ->label('النوع')
                            ->options([
                                'recitation' => 'تسميع جديد (حفظ)',
                                'revision' => 'مراجعة',
                            ])
                            ->inline()
                            ->required(),
                        \Filament\Forms\Components\TextInput::make('count')
                            ->label('عدد الصفحات')
                            ->numeric()
                            ->default(1)
                            ->required(),
                    ])
                    ->action(function ($record, array $data) {
                        \App\Models\PageLog::create([
                            'student_id' => $record->id,
                            'type' => $data['type'],
                            'count' => $data['count'],
                            'date' => \Carbon\Carbon::now(),
                        ]);
                    }),
                // Action::make('grant_points')
                //     ->label('منح')
                //     ->icon('heroicon-o-gift')
                //     ->form([
                //         \Filament\Forms\Components\TextInput::make('amount')->label('النقاط')->numeric()->required(),
                //         \Filament\Forms\Components\TextInput::make('reason')->label('السبب')->default('مكافأة'),
                //     ])
                //     ->action(function ($record, array $data) {
                //         \App\Models\PointTransaction::create([
                //             'student_id' => $record->id,
                //             'teacher_id' => auth()->user() ? auth()->id() : 1,
                //             'amount' => $data['amount'],
                //             'reason' => $data['reason'],
                //         ]);
                //     }),
                Action::make('grant_points')
    ->label('منح نقاط')
    ->icon('heroicon-o-gift')
    ->color('success') // Use that Lime Green feel
    ->modalHeading('إضافة أو خصم نقاط')
    ->modalWidth('md')
    ->form([
        Grid::make(2)->schema([
            // 1. Toggle for Adding/Removing
            Toggle::make('is_deduction')
                ->label('خصم / إضافة')
                // ->offLabel('إضافة (+)')
                // ->onLabel('خصم (-)')
                ->onColor('danger')
                ->offColor('success')
                ->offIcon('heroicon-m-plus-circle')
                ->onIcon('heroicon-m-minus-circle')
                ->columnSpanFull()
                ->live(),

            // 2. Quick Points Buttons
            ToggleButtons::make('quick_amount')
                ->label('اختر النقاط')
                ->options([
                    '5' => '5',
                    '10' => '10',
                    '20' => '20',
                    '50' => '50',
                    '100' => '100',
                    'custom' => 'قيمة أخرى',
                ])
                ->colors([
                    'custom' => 'gray',
                ])
                ->default('10')
                ->inline()
                ->columnSpanFull()
                ->live(),

            // 3. Custom Amount (Visible only if 'custom' is selected)
            TextInput::make('custom_amount')
                ->label('النقاط المخصصة')
                ->numeric()
                ->hidden(fn (Get $get) => $get('quick_amount') !== 'custom')
                ->required(fn (Get $get) => $get('quick_amount') === 'custom')
                ->columnSpanFull(),

            // 4. Quick Reason Buttons
            ToggleButtons::make('quick_reason')
                ->label('السبب الشائع')
                ->options([
                    'مكافأة' => 'مكافأة',
                    'مشاركة' => 'مشاركة',
                    'التزام' => 'التزام',
                    'خصم سلوك' => 'خصم سلوك',
                    'custom' => 'سبب آخر',
                ])
                ->default('مكافأة')
                ->inline()
                ->columnSpanFull()
                ->live(),

            // 5. Custom Reason (Visible only if 'custom' is selected)
            TextInput::make('custom_reason')
                ->label('اكتب السبب')
                ->hidden(fn (Get $get) => $get('quick_reason') !== 'custom')
                ->required(fn (Get $get) => $get('quick_reason') === 'custom')
                ->columnSpanFull(),
        ]),
    ])
    ->action(function ($record, array $data) {
        // Logic to determine amount
        $finalAmount = $data['quick_amount'] === 'custom' 
            ? (int) $data['custom_amount'] 
            : (int) $data['quick_amount'];

        // If it's a deduction, make the number negative
        if ($data['is_deduction']) {
            $finalAmount = -abs($finalAmount);
        }

        // Logic to determine reason
        $finalReason = $data['quick_reason'] === 'custom' 
            ? $data['custom_reason'] 
            : $data['quick_reason'];

        \App\Models\PointTransaction::create([
            'student_id' => $record->id,
            'teacher_id' => auth()->id() ?? 1,
            'amount' => $finalAmount,
            'reason' => $finalReason,
        ]);
    })
    ->successNotificationTitle('تمت العملية بنجاح'),
                Action::make('sura_tracker')
                    ->label('متابعة السور')
                    ->icon('heroicon-o-book-open')
                    ->color('info')
                    ->url(fn ($record) => '/app/students/' . $record->id . '/tracker'),
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
                        $students = \App\Models\Student::when(auth()->user()->role === 'teacher', function($q) {
                            $q->where('teacher_id', auth()->id());
                        })->get();

                        foreach ($students as $student) {
                            $existing = $student->attendances()->whereDate('date', \Carbon\Carbon::today())->first();
                            if (!$existing) {
                                $att = \App\Models\Attendance::create([
                                    'student_id' => $student->id,
                                    'date' => \Carbon\Carbon::today(),
                                    'is_present' => false,
                                ]);
                            }
                        }
                    }),
            ])
            ->defaultSort('name');
    }
}
