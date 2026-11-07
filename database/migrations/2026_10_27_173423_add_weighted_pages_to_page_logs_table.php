<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add weighted_pages to page_logs.
     *
     * This column stores the sum of each selected child's points_multiplier
     * (from the curriculum children JSON) at the moment the log entry is created.
     * It is the correct "page equivalent" value to use in all aggregations and
     * leaderboard widgets — replacing the raw `count` column which only counts
     * how many children were selected, ignoring their individual weight.
     *
     * Formula: SUM(child.points_multiplier) for every selected child.
     */
    public function up(): void
    {
        Schema::table('page_logs', function (Blueprint $table) {
            $table->double('weighted_pages')->nullable()->after('count');
        });

        // Backfill existing page_logs with their correct weighted page count
        $curricula = DB::table('curriculum')->get()->keyBy('id');

        DB::table('page_logs')->orderBy('id')->chunk(100, function ($logs) use ($curricula) {
            foreach ($logs as $log) {
                $weighted = null;
                $children = json_decode($log->children ?? '[]', true);

                if (! empty($children) && $log->curriculum_id && isset($curricula[$log->curriculum_id])) {
                    $curr = $curricula[$log->curriculum_id];
                    $currChildren = json_decode($curr->children ?? '[]', true) ?: [];

                    $sum = (float) collect($currChildren)
                        ->whereIn('label', $children)
                        ->sum('points_multiplier');

                    if ($sum > 0) {
                        $weighted = $sum;
                    }
                }

                if ($weighted === null) {
                    $weighted = (float) ($log->count ?? 0);
                }

                DB::table('page_logs')
                    ->where('id', $log->id)
                    ->update(['weighted_pages' => $weighted]);
            }
        });
    }

    public function down(): void
    {
        Schema::table('page_logs', function (Blueprint $table) {
            $table->dropColumn('weighted_pages');
        });
    }
};
