<?php

use App\Filament\Pages\SqlConsolePage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    Config::set('sql-console.enabled', true);
    Config::set('sql-console.write_mode', false);
    RateLimiter::clear('sql-console:1');
});

describe('SQL Console access control', function () {
    it('blocks access when kill switch is off', function () {
        Config::set('sql-console.enabled', false);

        $user = User::factory()->create([
            'role' => 'admin',
            'email' => 'admin@test.com',
        ]);
        Config::set('sql-console.allowed_email', 'admin@test.com');

        $this->actingAs($user);

        expect(SqlConsolePage::canAccess())->toBeFalse();
    });

    it('blocks access when allowed_email is empty', function () {
        Config::set('sql-console.allowed_email', '');

        $user = User::factory()->create(['role' => 'admin']);
        $this->actingAs($user);

        expect(SqlConsolePage::canAccess())->toBeFalse();
    });

    it('blocks access when user email does not match allowed email', function () {
        Config::set('sql-console.allowed_email', 'superadmin@example.com');

        $user = User::factory()->create([
            'role' => 'admin',
            'email' => 'other@example.com',
        ]);
        $this->actingAs($user);

        expect(SqlConsolePage::canAccess())->toBeFalse();
    });

    it('grants access when email matches and console is enabled', function () {
        Config::set('sql-console.allowed_email', 'superadmin@example.com');

        $user = User::factory()->create([
            'role' => 'admin',
            'email' => 'superadmin@example.com',
        ]);
        $this->actingAs($user);

        expect(SqlConsolePage::canAccess())->toBeTrue();
    });

    it('is never shown in navigation', function () {
        expect(SqlConsolePage::shouldRegisterNavigation())->toBeFalse();
    });
});

describe('SQL Console password confirmation', function () {
    function makeSuperAdmin(): User
    {
        Config::set('sql-console.allowed_email', 'sa@example.com');

        return User::factory()->create([
            'role' => 'admin',
            'email' => 'sa@example.com',
            'password' => Hash::make('secret123'),
        ]);
    }

    it('starts in unconfirmed state without a session entry', function () {
        $user = makeSuperAdmin();

        $component = Livewire::actingAs($user)->test(SqlConsolePage::class);

        expect($component->get('confirmed'))->toBeFalse();
    });

    it('rejects a wrong password', function () {
        $user = makeSuperAdmin();

        Livewire::actingAs($user)
            ->test(SqlConsolePage::class)
            ->call('confirmPassword', 'wrongpassword')
            ->assertNotified(); // danger notification fired

        expect(session()->get('auth.password_confirmed_at'))->toBeNull();
    });

    it('accepts the correct password and sets confirmed=true', function () {
        $user = makeSuperAdmin();

        $component = Livewire::actingAs($user)
            ->test(SqlConsolePage::class)
            ->call('confirmPassword', 'secret123');

        $component->assertNotified();
        expect($component->get('confirmed'))->toBeTrue();
        expect(session()->get('auth.password_confirmed_at'))->not->toBeNull();
    });

    it('resets confirmed when session timestamp is expired', function () {
        $user = makeSuperAdmin();

        // Set a timestamp 11 minutes in the past
        session()->put('auth.password_confirmed_at', time() - 661);

        $component = Livewire::actingAs($user)->test(SqlConsolePage::class);

        expect($component->get('confirmed'))->toBeFalse();
    });
});

describe('SQL Console query execution', function () {
    function confirmedSuperAdmin(): User
    {
        Config::set('sql-console.allowed_email', 'sa@example.com');
        session()->put('auth.password_confirmed_at', time());

        return User::factory()->create([
            'role' => 'admin',
            'email' => 'sa@example.com',
            'password' => Hash::make('secret123'),
        ]);
    }

    it('blocks execution when not confirmed', function () {
        $user = confirmedSuperAdmin();
        // Remove the confirmed session
        session()->forget('auth.password_confirmed_at');

        Livewire::actingAs($user)
            ->test(SqlConsolePage::class)
            ->set('sql', 'SELECT 1')
            ->call('executeQuery')
            ->assertSet('results', null);
    });

    it('rejects non-SELECT statements in read-only mode', function () {
        $user = confirmedSuperAdmin();

        $component = Livewire::actingAs($user)
            ->test(SqlConsolePage::class)
            ->set('sql', 'DELETE FROM users')
            ->call('executeQuery');

        expect($component->get('errorMessage'))->toContain('وضع القراءة فقط');
    });

    it('runs a basic SELECT and returns results', function () {
        $user = confirmedSuperAdmin();

        $component = Livewire::actingAs($user)
            ->test(SqlConsolePage::class)
            ->set('sql', 'SELECT 1 AS num')
            ->call('executeQuery');

        expect($component->get('errorMessage'))->toBeNull();
        expect($component->get('results'))->not->toBeNull();
        expect($component->get('rowCount'))->toBe(1);
    });

    it('clears results with clearResults', function () {
        $user = confirmedSuperAdmin();

        $component = Livewire::actingAs($user)
            ->test(SqlConsolePage::class)
            ->set('sql', 'SELECT 1 AS num')
            ->call('executeQuery')
            ->call('clearResults');

        expect($component->get('results'))->toBeNull();
        expect($component->get('sql'))->toBe('');
    });

    it('returns an error for empty queries', function () {
        $user = confirmedSuperAdmin();

        $component = Livewire::actingAs($user)
            ->test(SqlConsolePage::class)
            ->set('sql', '   ')
            ->call('executeQuery');

        expect($component->get('errorMessage'))->not->toBeEmpty();
    });
});
