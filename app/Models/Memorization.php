<?php

namespace App\Models;

use App\Enums\MemorizationType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Memorization extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $attributes = [
        'type' => MemorizationType::Regular,
    ];

    protected function casts(): array
    {
        return [
            'is_need_rememorisation' => 'boolean',
            'is_need_revision' => 'boolean',
            'needs_revision_children' => 'array',
            'needs_rememorisation_children' => 'array',
            'update_date' => 'date',
            'test_counts' => 'integer',
            'type' => MemorizationType::class,
        ];
    }

    /**
     * Check if this memorization record belongs to the current active Dawara.
     */
    public function isInCurrentDawara(): bool
    {
        $current = Dawara::current();

        return $current && $this->dawara_id === $current->id;
    }

    /**
     * Get points earned for this memorization in the current Dawara.
     * Returns null if the memorization is from another Dawara (hiding points).
     */
    public function getPointsEarnedAttribute(): ?int
    {
        if (! $this->isInCurrentDawara()) {
            return null;
        }

        return (int) PointTransaction::where('student_id', $this->student_id)
            ->where('curriculum_id', $this->curriculum_id)
            ->where('dawara_id', $this->dawara_id)
            ->sum('amount');
    }

    /**
     * Array representation hiding points for records outside the current Dawara.
     */
    public function toArray(): array
    {
        $attributes = parent::toArray();

        if (! $this->isInCurrentDawara()) {
            unset($attributes['points_earned']);
        } else {
            $attributes['points_earned'] = $this->points_earned;
        }

        return $attributes;
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function curriculum(): BelongsTo
    {
        return $this->belongsTo(Curriculum::class);
    }

    public function dawara(): BelongsTo
    {
        return $this->belongsTo(Dawara::class);
    }

    protected static function booted(): void
    {
        static::creating(function ($model) {
            if (empty($model->dawara_id)) {
                $currentDawara = Dawara::current();
                if ($currentDawara) {
                    $model->dawara_id = $currentDawara->id;
                }
            }
        });

        static::deleted(function (Memorization $memorization) {
            // Delete points associated with this student and curriculum item
            PointTransaction::where('student_id', $memorization->student_id)
                ->where('curriculum_id', $memorization->curriculum_id)
                ->delete();

            // Delete page logs associated with this student and curriculum item
            PageLog::where('student_id', $memorization->student_id)
                ->where('curriculum_id', $memorization->curriculum_id)
                ->delete();
        });
    }
}
