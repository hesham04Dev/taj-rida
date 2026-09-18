<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Memorization extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'is_need_rememorisation' => 'boolean',
            'is_need_revision' => 'boolean',
            'need_from_page' => 'integer',
            'need_to_page' => 'integer',
            'update_date' => 'date',
            'test_counts' => 'integer',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function curriculum(): BelongsTo
    {
        return $this->belongsTo(Curriculum::class);
    }

    protected static function booted(): void
    {
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
