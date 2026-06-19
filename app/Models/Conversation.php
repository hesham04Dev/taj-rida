<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Conversation extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function guardian(): BelongsTo
    {
        return $this->belongsTo(Guardian::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class)->orderBy('created_at');
    }

    /**
     * Count unread messages for a given side.
     *
     * @param  'teacher'|'guardian'  $side
     */
    public function unreadCountFor(string $side, int $id): int
    {
        // Messages NOT sent by the given side that are unread
        return $this->hasMany(Message::class)
            ->where('sender_type', '!=', $side)
            ->whereNull('read_at')
            ->count();
    }

    /**
     * Get or create the conversation for a teacher-student-guardian triplet.
     */
    public static function firstOrCreateFor(int $studentId, int $teacherId, int $guardianId): self
    {
        return static::firstOrCreate([
            'student_id' => $studentId,
            'teacher_id' => $teacherId,
            'guardian_id' => $guardianId,
        ]);
    }
}
