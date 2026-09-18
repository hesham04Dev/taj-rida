<?php

namespace App\Models;

use Database\Factories\CreditorFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Creditor extends Model
{
    /** @use HasFactory<CreditorFactory> */
    use HasFactory;

    protected $guarded = [];

    public function budgetTransactions()
    {
        return $this->hasMany(BudgetTransaction::class);
    }

    public function getTotalCreditAttribute(): float
    {
        return (float) $this->budgetTransactions()->where('type', 'credit')->sum('amount');
    }

    public function getTotalReturnedAttribute(): float
    {
        return (float) $this->budgetTransactions()->where('type', 'return')->sum('amount');
    }

    public function getRemainingBalanceAttribute(): float
    {
        return max(0, $this->total_credit - $this->total_returned);
    }
}
