<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Guardian extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $guarded = [];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function studentGuardians()
    {
        return $this->hasMany(StudentGuardian::class, 'phone', 'phone');
    }

    public function getStudentsAttribute()
    {
        $studentIds = $this->studentGuardians()->pluck('student_id');

        return Student::withoutGlobalScopes()->whereIn('id', $studentIds)->get();
    }
}
