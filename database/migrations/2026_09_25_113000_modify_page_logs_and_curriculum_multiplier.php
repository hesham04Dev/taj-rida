<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Update existing curriculum JSON
        $curriculums = DB::table('curriculum')->get();
        foreach ($curriculums as $curriculum) {
            $children = json_decode($curriculum->children, true);
            if (is_array($children)) {
                foreach ($children as &$child) {
                    if (array_key_exists('points_multiplier', $child)) {
                        $child['pages_equivalent'] = $child['points_multiplier'];
                        unset($child['points_multiplier']);
                    }
                }
                DB::table('curriculum')
                    ->where('id', $curriculum->id)
                    ->update(['children' => json_encode($children)]);
            }
        }

        // 2. Modify page_logs table
        Schema::table('page_logs', function (Blueprint $table) {
            $table->json('children')->nullable()->after('type');
        });

        // Convert existing from_page/to_page to children if necessary,
        // but given the requirement we might just drop them and the old logs might lose specific context
        // or we could leave them for historical logs. But the user asked to "get raid from page and to_page in the page log"
        Schema::table('page_logs', function (Blueprint $table) {
            $table->dropColumn(['from_page', 'to_page']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('page_logs', function (Blueprint $table) {
            $table->string('from_page')->nullable()->after('type');
            $table->float('to_page')->nullable()->after('from_page');
        });

        Schema::table('page_logs', function (Blueprint $table) {
            $table->dropColumn('children');
        });

        // Reverse curriculum JSON
        $curriculums = DB::table('curriculum')->get();
        foreach ($curriculums as $curriculum) {
            $children = json_decode($curriculum->children, true);
            if (is_array($children)) {
                foreach ($children as &$child) {
                    if (array_key_exists('pages_equivalent', $child)) {
                        $child['points_multiplier'] = $child['pages_equivalent'];
                        unset($child['pages_equivalent']);
                    }
                }
                DB::table('curriculum')
                    ->where('id', $curriculum->id)
                    ->update(['children' => json_encode($children)]);
            }
        }
    }
};
