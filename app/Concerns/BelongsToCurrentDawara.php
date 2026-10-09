<?php

namespace App\Concerns;

use App\Models\Dawara;
use Illuminate\Database\Eloquent\Builder;

trait BelongsToCurrentDawara
{
    /**
     * Boot the trait to apply global scope and handle creating event.
     */
    protected static function bootBelongsToCurrentDawara()
    {
        static::addGlobalScope('current_dawara', function (Builder $builder) {
            $currentDawara = Dawara::current();
            if ($currentDawara) {
                $builder->where('dawara_id', $currentDawara->id);
            } else {
                // If there's no active dawara, ensure queries return nothing
                // because this scope requires an active dawara.
                $builder->whereRaw('1 = 0');
            }
        });

        // Automatically set dawara_id when creating a new record
        static::creating(function ($model) {
            if (empty($model->dawara_id)) {
                $currentDawara = Dawara::current();
                if ($currentDawara) {
                    $model->dawara_id = $currentDawara->id;
                }
            }
        });
    }

    /**
     * Scope a query to a specific Dawara (bypassing the global scope).
     *
     * @param  Dawara|int  $dawara
     * @return Builder
     */
    public function scopeForDawara(Builder $query, $dawara)
    {
        $dawaraId = $dawara instanceof Dawara ? $dawara->id : $dawara;

        return $query->withoutGlobalScope('current_dawara')->where('dawara_id', $dawaraId);
    }
}
