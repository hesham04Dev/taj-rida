<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Fortify\TwoFactorAuthenticatable;

#[Fillable(['name', 'email', 'password', 'selected_classroom_id'])]
#[Hidden(['password', 'two_factor_secret', 'two_factor_recovery_codes', 'remember_token'])]
class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, TwoFactorAuthenticatable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn ($word) => Str::substr($word, 0, 1))
            ->implode('');
    }

    public function students()
    {
        return $this->hasMany(Student::class, 'teacher_id');
    }

    public function classrooms()
    {
        return $this->belongsToMany(Classroom::class, 'classroom_users', 'user_id', 'classroom_id')
            ->withPivot('is_default')->withTimestamps();
    }

    public function defaultClassroom(): ?Classroom
    {
        // Prefer the classroom the teacher explicitly set as default; fall back to any classroom they belong to.
        return $this->classrooms()->wherePivot('is_default', true)->first()
            ?? $this->classrooms()->first();
    }

    /** The classroom the teacher has selected as their current active view. */
    public function selectedClassroom()
    {
        return $this->belongsTo(Classroom::class, 'selected_classroom_id');
    }

    /**
     * Resolve the classroom this teacher should be viewing right now.
     * Priority: explicitly selected → default → first available.
     */
    public function activeClassroom(): ?Classroom
    {
        return $this->selectedClassroom
            ?? $this->defaultClassroom();
    }

    public function pointTransactions(): HasMany
    {
        return $this->hasMany(PointTransaction::class, 'teacher_id');
    }

    public function sentNotifications(): HasMany
    {
        return $this->hasMany(Notification::class, 'teacher_id');
    }

    public function conversations(): HasMany
    {
        return $this->hasMany(Conversation::class, 'teacher_id');
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }
}
