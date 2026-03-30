<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sura extends Model
{
    protected $guarded = [];

    public function recitations()
    {
        return $this->hasMany(Recitation::class);
    }

    public function revisions()
    {
        return $this->hasMany(Revision::class);
    }
}
