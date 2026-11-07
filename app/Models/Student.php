<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

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

    // // history of all classes
    // public function classrooms()
    // {
    //     return $this->belongsToMany(Classroom::class, 'student_classroom')
    //         ->withPivot('is_current')
    //         ->withTimestamps();
    // }

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

    public function guardians(): HasMany
    {
        return $this->hasMany(StudentGuardian::class);
    }

    protected static function booted()
    {
        static::addGlobalScope('teacher', function (Builder $builder) {
            if (! auth()->check() || auth()->user()->role === 'admin') {
                return;
            }

            $user = auth()->user();

            // If the teacher has chosen a specific classroom, filter by that classroom.
            if ($user->selected_classroom_id) {
                $builder->where('classroom_id', $user->selected_classroom_id);

                return;
            }

            // Fallback: show students from any classroom the teacher belongs to.
            $classroomIds = $user->classrooms()->pluck('classrooms.id');

            if ($classroomIds->isNotEmpty()) {
                $builder->whereIn('classroom_id', $classroomIds);
            } else {
                // Legacy fallback: teacher_id based filter.
                $builder->where('teacher_id', $user->id);
            }
        });

        static::saved(function (Student $student) {
            if (! $student->wasRecentlyCreated && ! $student->wasChanged('classroom_id')) {
                return;
            }

            DB::transaction(function () use ($student) {
                // close any current row that is not the new class
                DB::table('student_classroom')
                    ->where('student_id', $student->id)
                    ->where('is_current', true)
                    ->where('classroom_id', '!=', $student->classroom_id ?? 0)
                    ->update(['is_current' => false, 'updated_at' => now()]);

                if (! $student->classroom_id) {
                    return;
                }

                $alreadyCurrent = DB::table('student_classroom')
                    ->where('student_id', $student->id)
                    ->where('classroom_id', $student->classroom_id)
                    ->where('is_current', true)->exists();

                if (! $alreadyCurrent) {
                    DB::table('student_classroom')->insert([
                        'student_id' => $student->id,
                        'classroom_id' => $student->classroom_id,
                        'is_current' => true,
                        'created_at' => now(), 'updated_at' => now(),
                    ]);
                }
            });
        });
    }

    public function classrooms()
    {
        return $this->belongsToMany(Classroom::class, 'student_classroom')
            ->withPivot('is_current')
            ->withTimestamps()
            ->orderByPivot('created_at', 'desc');
    }

    public function classroom()
    {
        return $this->belongsTo(Classroom::class);

    }

    public function dawaras(): BelongsToMany
    {
        return $this->belongsToMany(Dawara::class)
            ->withPivot('total_points', 'final_grade', 'summary_notes')
            ->withTimestamps();
    }
}
