<?php

namespace App\Models;

use Database\Factories\BudgetTransactionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BudgetTransaction extends Model
{
    /** @use HasFactory<BudgetTransactionFactory> */
    use HasFactory;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'amount' => 'decimal:2',
        ];
    }

    public function creditor()
    {
        return $this->belongsTo(Creditor::class);
    }
}
