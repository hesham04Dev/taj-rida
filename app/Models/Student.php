<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Student extends Model implements AuthenticatableContract
{
    use Authenticatable, HasFactory;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'birthdate' => 'date',
            'given_points' => 'integer',
            'access_code' => 'string',
        ];
    }

    /**
     * The fixed card denominations available for physical card distribution.
     *
     * @var array<int>
     */
    public const CARD_DENOMINATIONS = [100, 50, 25, 15];

    /**
     * Compute the current total points from transactions.
     */
    public function getTotalPointsAttribute(): int
    {
        return (int) $this->pointTransactions()->sum('amount');
    }

    /**
     * Compute the remaining balance (total earned - already given out).
     */
    public function getRemainingPointsAttribute(): int
    {
        return max(0, $this->total_points - $this->given_points);
    }

    /**
     * Return the largest card denomination that fits within the remaining balance.
     * Falls back to the smallest denomination if nothing fits.
     */
    public function getSuggestedCardValueAttribute(): int
    {
        $remaining = $this->remaining_points;

        foreach (self::CARD_DENOMINATIONS as $card) {
            if ($card <= $remaining) {
                return $card;
            }
        }

        return self::CARD_DENOMINATIONS[count(self::CARD_DENOMINATIONS) - 1];
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function memorizations(): HasMany
    {
        return $this->hasMany(Memorization::class);
    }

    public function pageLogs(): HasMany
    {
        return $this->hasMany(PageLog::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function pointTransactions()
    {
        return $this->hasMany(PointTransaction::class);
    }

    public function studentNotes()
    {
        return $this->hasMany(StudentNote::class);
    }

    protected static function booted()
    {
        static::addGlobalScope('teacher', function (Builder $builder) {
            if (auth()->check() && auth()->user()->role !== 'admin') {
                $builder->where('teacher_id', auth()->id());
            }
        });
    }
}
