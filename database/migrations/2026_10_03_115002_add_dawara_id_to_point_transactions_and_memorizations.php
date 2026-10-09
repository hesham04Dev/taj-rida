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
        // 1. Add dawara_id as NULLABLE first
        Schema::table('point_transactions', function (Blueprint $table) {
            $table->unsignedBigInteger('dawara_id')->nullable()->after('id');
        });

        Schema::table('memorizations', function (Blueprint $table) {
            $table->unsignedBigInteger('dawara_id')->nullable()->after('id');
        });

        // Get the legacy dawara ID
        $legacyDawaraId = DB::table('dawaras')->where('name', 'Legacy Dawara')->value('id');

        if ($legacyDawaraId) {
            // 2. Backfill existing rows (chunked/batched to prevent huge locks on big tables)
            DB::table('point_transactions')->whereNull('dawara_id')->orderBy('id')->chunk(1000, function ($transactions) use ($legacyDawaraId) {
                DB::table('point_transactions')->whereIn('id', $transactions->pluck('id'))->update(['dawara_id' => $legacyDawaraId]);
            });

            DB::table('memorizations')->whereNull('dawara_id')->orderBy('id')->chunk(1000, function ($memorizations) use ($legacyDawaraId) {
                DB::table('memorizations')->whereIn('id', $memorizations->pluck('id'))->update(['dawara_id' => $legacyDawaraId]);
            });
        }

        // 3. Sanity check: Throw an exception if any nulls remain before applying constraints
        $nullTransactions = DB::table('point_transactions')->whereNull('dawara_id')->count();
        $nullMemorizations = DB::table('memorizations')->whereNull('dawara_id')->count();

        if ($nullTransactions > 0 || $nullMemorizations > 0) {
            throw new Exception("Cannot make dawara_id NOT NULL. There are $nullTransactions point_transactions and $nullMemorizations memorizations with NULL dawara_id.");
        }

        // 4. Make column NOT NULL, add index and FK constraint
        Schema::table('point_transactions', function (Blueprint $table) {
            $table->foreign('dawara_id')->references('id')->on('dawaras')->restrictOnDelete();
            $table->unsignedBigInteger('dawara_id')->nullable(false)->change();
        });

        Schema::table('memorizations', function (Blueprint $table) {
            // Existing unique constraint is ['student_id', 'curriculum_id'], we need to drop it and add dawara_id to it
            // Assuming default naming convention for unique constraint: table_col1_col2_unique
            // $table->dropUnique('memorizations_student_id_curriculum_id_unique');

            $table->foreign('dawara_id')->references('id')->on('dawaras')->restrictOnDelete();
            $table->unsignedBigInteger('dawara_id')->nullable(false)->change();

            $table->unique(['dawara_id', 'student_id', 'curriculum_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('memorizations', function (Blueprint $table) {
            $table->dropUnique(['dawara_id', 'student_id', 'curriculum_id']);
            $table->dropForeign(['dawara_id']);
            $table->dropColumn('dawara_id');
            $table->unique(['student_id', 'curriculum_id']);
        });

        Schema::table('point_transactions', function (Blueprint $table) {
            $table->dropForeign(['dawara_id']);
            $table->dropColumn('dawara_id');
        });
    }
};
