<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use BackedEnum;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class ManageSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected string $view = 'filament.pages.manage-settings';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static ?int $navigationSort = 3;

    public static function getNavigationLabel(): string
    {
        return 'الإعدادات العامة';
    }

    public function getTitle(): string
    {
        return 'الإعدادات العامة للبرنامج';
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->role === 'admin';
    }

    public ?array $data = [];

    public function mount(): void
    {
        $settings = Setting::all()->pluck('value', 'key')->toArray();

        // Decode JSON arrays like point_reasons if they exist
        if (isset($settings['point_reasons'])) {
            $settings['point_reasons'] = json_decode($settings['point_reasons'], true) ?? [];
        } else {
            $settings['point_reasons'] = [
                ['reason' => 'مكافأة', 'amount' => 10, 'type' => 'addition'],
                ['reason' => 'مشاركة', 'amount' => 15, 'type' => 'addition'],
                ['reason' => 'التزام', 'amount' => 20, 'type' => 'addition'],
                ['reason' => 'خصم سلوك', 'amount' => 10, 'type' => 'deduction'],
                ['reason' => 'مثير للمشاكل', 'amount' => 15, 'type' => 'deduction'],
            ];
        }

        $this->form->fill($settings);
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema([
                Tabs::make('Settings')
                    ->tabs([
                        Tab::make('points_settings')
                            ->label('نقاط الحلقات')
                            ->icon('heroicon-o-star')
                            ->schema([
                                TextInput::make('recitation_points_per_page')
                                    ->label('نقاط التسميع لكل صفحة')
                                    ->numeric()
                                    ->required()
                                    ->default(10),
                                TextInput::make('revision_points_per_page')
                                    ->label('نقاط المراجعة لكل صفحة')
                                    ->numeric()
                                    ->required()
                                    ->default(5),
                                TextInput::make('attendance_points')
                                    ->label('نقاط الحضور اليومي')
                                    ->numeric()
                                    ->required()
                                    ->default(5),
                                TextInput::make('absence_penalty')
                                    ->label('خصم الغياب (أدخل قيمة سالبة)')
                                    ->numeric()
                                    ->required()
                                    ->default(-5),
                            ])->columns(2),

                        Tab::make('grading_settings')
                            ->label('نسب تقييم التسميع والمراجعة')
                            ->icon('heroicon-o-academic-cap')
                            ->schema([
                                TextInput::make('grade_excellent_percent')
                                    ->label('ممتاز (%)')
                                    ->numeric()
                                    ->required()
                                    ->suffix('%')
                                    ->default(100),
                                TextInput::make('grade_very_good_percent')
                                    ->label('جيد جداً (%)')
                                    ->numeric()
                                    ->required()
                                    ->suffix('%')
                                    ->default(75),
                                TextInput::make('grade_good_percent')
                                    ->label('جيد (%)')
                                    ->numeric()
                                    ->required()
                                    ->suffix('%')
                                    ->default(50),
                                TextInput::make('grade_acceptable_percent')
                                    ->label('مقبول (%)')
                                    ->numeric()
                                    ->required()
                                    ->suffix('%')
                                    ->default(25),
                                TextInput::make('grade_weak_percent')
                                    ->label('ضعيف (%)')
                                    ->numeric()
                                    ->required()
                                    ->suffix('%')
                                    ->default(0),
                            ])->columns(2),

                        Tab::make('repetition_settings')
                            ->label('نسب التكرار (إعادة التسميع والمراجعة)')
                            ->icon('heroicon-o-arrow-path')
                            ->schema([
                                TextInput::make('re_recitation_percent')
                                    ->label('نسبة النقاط لإعادة التسميع (التكرار)')
                                    ->numeric()
                                    ->required()
                                    ->suffix('%')
                                    ->helperText('النسبة المئوية الممنوحة من نقاط التسميع الأصلية عند تكرار نفس الصفحة')
                                    ->default(50),
                                TextInput::make('re_revision_percent')
                                    ->label('نسبة النقاط لإعادة المراجعة (التكرار)')
                                    ->numeric()
                                    ->required()
                                    ->suffix('%')
                                    ->helperText('النسبة المئوية الممنوحة من نقاط المراجعة الأصلية عند تكرار نفس الصفحة')
                                    ->default(50),
                            ])->columns(2),

                        Tab::make('reasons_settings')
                            ->label('أسباب منح/خصم النقاط')
                            ->icon('heroicon-o-gift')
                            ->schema([
                                Repeater::make('point_reasons')
                                    ->label('الأسباب المعرّفة مسبقاً')
                                    ->itemLabel(fn (array $state): ?string => ($state['reason'] ?? null).(($state['amount'] ?? null) ? ' ('.($state['type'] === 'deduction' ? '-' : '+').$state['amount'].')' : ''))
                                    ->schema([
                                        TextInput::make('reason')
                                            ->label('السبب')
                                            ->required()
                                            ->placeholder('مثال: مشاغبة، تميز بالصف، حضور مبكر'),
                                        TextInput::make('amount')
                                            ->label('النقاط')
                                            ->numeric()
                                            ->required()
                                            ->placeholder('القيمة المطلقة للعلامات'),
                                        Select::make('type')
                                            ->label('النوع')
                                            ->options([
                                                'addition' => 'إضافة (+)',
                                                'deduction' => 'خصم (-)',
                                            ])
                                            ->required()
                                            ->default('addition'),
                                    ])
                                    ->columns(3)
                                    ->default([
                                        ['reason' => 'مكافأة', 'amount' => 10, 'type' => 'addition'],
                                        ['reason' => 'مشاركة', 'amount' => 15, 'type' => 'addition'],
                                        ['reason' => 'التزام', 'amount' => 20, 'type' => 'addition'],
                                        ['reason' => 'خصم سلوك', 'amount' => 10, 'type' => 'deduction'],
                                        ['reason' => 'مثير للمشاكل', 'amount' => 15, 'type' => 'deduction'],
                                    ])
                                    ->createItemButtonLabel('إضافة سبب جديد'),
                            ]),

                        Tab::make('system_settings')
                            ->label('إعدادات النظام')
                            ->icon('heroicon-o-computer-desktop')
                            ->schema([
                                Select::make('login_type')
                                    ->label('طريقة تسجيل الدخول')
                                    ->options([
                                        'email' => 'البريد الإلكتروني',
                                        'name' => 'الاسم (كتابة)',
                                        'name_select' => 'الاسم (اختيار من قائمة)',
                                    ])
                                    ->required()
                                    ->default('email'),
                            ])->columns(1),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        foreach ($data as $key => $value) {
            if ($key === 'point_reasons') {
                $value = json_encode($value, JSON_UNESCAPED_UNICODE);
            }
            Setting::updateOrCreate(['key' => $key], ['value' => (string) $value]);
        }

        Notification::make()
            ->title('تم حفظ الإعدادات بنجاح')
            ->success()
            ->send();
    }
}
