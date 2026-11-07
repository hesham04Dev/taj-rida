<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Classroom extends Model
{
    protected $fillable = ['name', 'dawara_id'];

    /** Teachers (users) assigned to this classroom. */
    public function users()
    {
        return $this->belongsToMany(User::class, 'classroom_users', 'classroom_id', 'user_id')
            ->withPivot('is_default')->withTimestamps();
    }

    public function teachers()
    {
        return $this->belongsToMany(User::class, 'classroom_users', 'classroom_id', 'user_id')
            ->withPivot('is_default')->withTimestamps();
    }

    /**
     * Students directly assigned to this classroom via the FK on students.
     * Use this for the current/primary assignment.
     */
    public function students()
    {
        return $this->hasMany(Student::class);
    }

    /** Full class-history pivot (student_classroom). */
    public function studentHistory()
    {
        return $this->belongsToMany(Student::class, 'student_classroom')
            ->withPivot('is_current')
            ->withTimestamps();
    }

    public function dawara()
    {
        return $this->belongsTo(Dawara::class);
    }

    /**
     * Scope: classrooms whose dawara is active, OR classrooms with no dawara.
     * Falls back gracefully when no dawara is configured.
     */
    public function scopeActive($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('dawara_id')
                ->orWhereHas('dawara', fn ($d) => $d->where('status', 'active'));
        });
    }

    public function getIsActiveAttribute(): bool
    {
        return $this->dawara_id === null || (bool) $this->dawara?->is_active;
    }
}
