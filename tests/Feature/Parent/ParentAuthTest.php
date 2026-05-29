<?php

use App\Livewire\Parent\Login;
use App\Models\Guardian;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;

use function Pest\Laravel\get;
use function Pest\Laravel\post;

uses(RefreshDatabase::class);

it('shows the parent login page', function (): void {
    get(route('parent.login'))->assertOk();
});

it('renders the Login Livewire component on parent login page', function (): void {
    get(route('parent.login'))
        ->assertSeeLivewire(Login::class);
});

it('redirects unauthenticated parents away from the dashboard', function (): void {
    get(route('parent.dashboard'))
        ->assertRedirect(route('parent.login'));
});

it('logs in a parent with valid credentials', function (): void {
    $parent = Guardian::create([
        'name' => 'أحمد',
        'phone' => '0599000000',
        'password' => Hash::make('password123'),
    ]);

    Livewire::test(Login::class)
        ->set('phone', '0599000000')
        ->set('password', 'password123')
        ->call('submit')
        ->assertRedirect(route('parent.dashboard'));

    expect(Auth::guard('guardian')->id())
        ->toBe($parent->id);
});

it('rejects invalid credentials', function (): void {
    Guardian::create([
        'name' => 'أحمد',
        'phone' => '0599000000',
        'password' => Hash::make('password123'),
    ]);

    Livewire::test(Login::class)
        ->set('phone', '0599000000')
        ->set('password', 'wrongpassword')
        ->call('submit')
        ->assertHasErrors(['phone']);
});

it('logs out a parent and redirects to login', function (): void {
    $parent = Guardian::create([
        'name' => 'أحمد',
        'phone' => '0599000000',
        'password' => Hash::make('password123'),
    ]);

    Auth::guard('guardian')->login($parent);

    post(route('parent.logout'))
        ->assertRedirect(route('parent.login'));

    expect(Auth::guard('guardian')->check())->toBeFalse();
});
