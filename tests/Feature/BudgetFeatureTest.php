<?php

use App\Models\BudgetTransaction;
use App\Models\Creditor;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('calculates creditor remaining balance correctly', function () {
    $creditor = Creditor::create(['name' => 'John Doe']);

    // Add 1000 credit
    BudgetTransaction::create([
        'creditor_id' => $creditor->id,
        'type' => 'credit',
        'amount' => 1000,
        'date' => now(),
    ]);

    // Return 300
    BudgetTransaction::create([
        'creditor_id' => $creditor->id,
        'type' => 'return',
        'amount' => 300,
        'date' => now(),
    ]);

    // Add 200 gift (should not affect balance)
    BudgetTransaction::create([
        'creditor_id' => $creditor->id,
        'type' => 'gift',
        'amount' => 200,
        'date' => now(),
    ]);

    expect($creditor->total_credit)->toBe(1000.0)
        ->and($creditor->total_returned)->toBe(300.0)
        ->and($creditor->remaining_balance)->toBe(700.0);
});
