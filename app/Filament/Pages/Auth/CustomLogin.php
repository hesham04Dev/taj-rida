<?php

namespace App\Filament\Pages\Auth;

use App\Models\Setting;
use App\Models\User;
// use Filament\Forms\Components\Select;
// use Filament\Forms\Components\TextInput;
// use Filament\Forms\Form;
use Filament\Auth\Pages\Login as BaseLogin;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CustomLogin extends BaseLogin
{
    public function form(Schema $form): Schema
    {
        // Get your setting (defaulting to 'email' if not set)
        $loginMode = Setting::where('key', 'login_type')->first();
        $loginMode = $loginMode ? $loginMode->value : 'email';

        return $form
            ->schema([
                $this->getLoginField($loginMode),
                $this->getPasswordFormComponent(),
                $this->getRememberFormComponent(),
            ])
            ->statePath('data');
    }

    protected function getLoginField($mode)
    {
        return match ($mode) {
            'name_select' => Select::make('login_identifier')
                ->label(__('Select User'))
                ->options(User::pluck('name', 'name')) // Or use 'id' if you prefer
                ->required()
                ->searchable(),

            'name' => TextInput::make('login_identifier')
                ->label(__('Name'))
                ->required()
                ->autocomplete('username'),

            default => $this->getEmailFormComponent(),
        };
    }

    protected function getCredentialsFromFormData(array $data): array
    {
        $loginMode = Setting::where('key', 'login_type')->first();
        $loginMode = $loginMode ? $loginMode->value : 'email';

        // Map the custom field back to the database column
        $field = ($loginMode === 'email') ? 'email' : 'name';

        return [
            $field => $data['login_identifier'] ?? $data['email'],
            'password' => $data['password'],
        ];
    }
}
