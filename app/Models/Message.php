<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'read_at' => 'datetime',
        ];
    }

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    /**
     * Whether this message was sent by a teacher.
     */
    public function isFromTeacher(): bool
    {
        return $this->sender_type === 'teacher';
    }

    /**
     * Whether this message was sent by a guardian.
     */
    public function isFromGuardian(): bool
    {
        return $this->sender_type === 'guardian';
    }
}
