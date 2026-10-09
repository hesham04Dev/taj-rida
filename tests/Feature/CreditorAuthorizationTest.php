<?php

use App\Models\Creditor;
use App\Models\Dawara;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->admin = User::factory()->create(['role' => 'admin']);
    $this->teacher = User::factory()->create(['role' => 'teacher']);
    $this->creditor = Creditor::create(['name' => 'Test Creditor']);

    // Ensure there is an active Dawara so the middleware is satisfied
    Dawara::create(['name' => 'Test Dawara', 'status' => Dawara::STATUS_ACTIVE]);
});

it('allows admins to view creditors list', function () {
    $this->actingAs($this->admin)
        ->get(route('filament.admin.resources.creditors.index'))
        ->assertSuccessful();
});

it('denies teachers from viewing creditors list', function () {
    $this->actingAs($this->teacher)
        ->get(route('filament.admin.resources.creditors.index'))
        ->assertForbidden();
});

it('allows admins to view a creditor', function () {
    $this->actingAs($this->admin)
        ->get(route('filament.admin.resources.creditors.edit', $this->creditor))
        ->assertSuccessful();
});

it('denies teachers from viewing a creditor', function () {
    $this->actingAs($this->teacher)
        ->get(route('filament.admin.resources.creditors.edit', $this->creditor))
        ->assertForbidden();
});
