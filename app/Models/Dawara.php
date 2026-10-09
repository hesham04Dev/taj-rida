<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Dawara extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    const STATUS_ACTIVE = 'active';

    const STATUS_COMPLETED = 'completed';

    protected static $currentDawara = null;

    /**
     * Scope a query to only include active dawaras.
     */
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    /**
     * Get the current active Dawara.
     * Returns null if none are found.
     */
    public static function current(): ?Dawara
    {
        if (self::$currentDawara) {
            return self::$currentDawara;
        }

        $activeDawaras = self::active()->get();

        if ($activeDawaras->isEmpty()) {
            return null;
        }

        self::$currentDawara = $activeDawaras->last();

        return self::$currentDawara;
    }

    /**
     * Clear the current cached Dawara (e.g., after ending one).
     */
    public static function clearCurrentCache(): void
    {
        self::$currentDawara = null;
    }

    public function pointTransactions(): HasMany
    {
        return $this->hasMany(PointTransaction::class);
    }

    public function memorizations(): HasMany
    {
        return $this->hasMany(Memorization::class);
    }

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(Student::class)
            ->withPivot('total_points', 'final_grade', 'summary_notes')
            ->withTimestamps();
    }

    protected static function booted(): void
    {
        static::saved(function (Dawara $dawara) {
            if ($dawara->status === self::STATUS_ACTIVE) {
                // Ensure no other Dawara is active
                Dawara::where('id', '!=', $dawara->id)
                    ->where('status', self::STATUS_ACTIVE)
                    ->update(['status' => self::STATUS_COMPLETED]);
            }

            static::clearCurrentCache();
        });

        static::deleted(function () {
            static::clearCurrentCache();
        });
    }
}
