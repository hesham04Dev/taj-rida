<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Curriculum extends Model
{
    use HasFactory;

    protected $table = 'curriculum';

    protected $fillable = ['number', 'name', 'children'];

    protected function casts(): array
    {
        return [
            'number' => 'integer',
            'children' => 'array',
        ];
    }

    public function memorizations(): HasMany
    {
        return $this->hasMany(Memorization::class);
    }

    public function pageLogs(): HasMany
    {
        return $this->hasMany(PageLog::class);
    }

    public function pointTransactions(): HasMany
    {
        return $this->hasMany(PointTransaction::class);
    }

    /**
     * Returns the average points_multiplier for a given list of child labels.
     *
     * @param  array<string>  $selectedLabels
     */
    public function averageMultiplierForLabels(array $selectedLabels): float
    {
        if (empty($selectedLabels)) {
            return 1.0;
        }

        $multipliers = collect($this->children)
            ->whereIn('label', $selectedLabels)
            ->pluck('points_multiplier')
            ->map(fn ($v) => (float) $v);

        return $multipliers->isNotEmpty() ? $multipliers->avg() : 1.0;
    }
}
