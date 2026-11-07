<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PageLog extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'children' => 'array',
            'weighted_pages' => 'float',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (PageLog $pageLog) {
            if ($pageLog->weighted_pages === null) {
                if ($pageLog->curriculum_id && ! empty($pageLog->children)) {
                    $curriculum = $pageLog->curriculum ?: Curriculum::find($pageLog->curriculum_id);
                    if ($curriculum) {
                        $sum = $curriculum->totalPagesEquivalentForLabels($pageLog->children);
                        if ($sum > 0) {
                            $pageLog->weighted_pages = $sum;
                        }
                    }
                }

                if ($pageLog->weighted_pages === null) {
                    $pageLog->weighted_pages = (float) ($pageLog->count ?? 0);
                }
            }
        });
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function curriculum(): BelongsTo
    {
        return $this->belongsTo(Curriculum::class);
    }
}
