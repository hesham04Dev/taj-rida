<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Student extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'birthdate' => 'date',
        ];
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function recitations()
    {
        return $this->hasMany(Recitation::class);
    }

    public function revisions()
    {
        return $this->hasMany(Revision::class);
    }

    public function pageLogs()
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
