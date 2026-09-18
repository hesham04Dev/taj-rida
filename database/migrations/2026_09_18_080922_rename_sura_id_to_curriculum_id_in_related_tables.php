<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Renames sura_id → curriculum_id in memorizations, page_logs, and
     * point_transactions. Also drops the old suras table.
     * Safe to run on an empty database.
     */
    public function up(): void
    {
        // 1. Drop old FK + rename in memorizations
        Schema::table('memorizations', function (Blueprint $table) {
            // Drop both foreign keys because the student_id FK relies on the unique index
            $table->dropForeign(['student_id']);
            $table->dropForeign(['sura_id']);
        });

        Schema::table('memorizations', function (Blueprint $table) {
            $table->dropUnique(['student_id', 'sura_id']);
            $table->renameColumn('sura_id', 'curriculum_id');
        });

        Schema::table('memorizations', function (Blueprint $table) {
            $table->foreign('student_id')->references('id')->on('students')->cascadeOnDelete();
            $table->foreign('curriculum_id')->references('id')->on('curriculum')->cascadeOnDelete();
            $table->unique(['student_id', 'curriculum_id']);
        });

        // 2. Drop old FK + rename in page_logs
        Schema::table('page_logs', function (Blueprint $table) {
            $table->dropForeign(['sura_id']);
            $table->renameColumn('sura_id', 'curriculum_id');
        });

        Schema::table('page_logs', function (Blueprint $table) {
            $table->foreign('curriculum_id')->references('id')->on('curriculum')->nullOnDelete();
        });

        // 3. Drop old FK + rename in point_transactions
        Schema::table('point_transactions', function (Blueprint $table) {
            $table->dropForeign(['sura_id']);
            $table->renameColumn('sura_id', 'curriculum_id');
        });

        Schema::table('point_transactions', function (Blueprint $table) {
            $table->foreign('curriculum_id')->references('id')->on('curriculum')->nullOnDelete();
        });

        // 4. Drop the old suras table (no longer needed)
        Schema::dropIfExists('suras');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recreate suras table
        Schema::create('suras', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->float('pages_count');
            $table->float('from_page');
            $table->float('to_page');
            $table->timestamps();
        });

        Schema::table('point_transactions', function (Blueprint $table) {
            $table->dropForeign(['curriculum_id']);
            $table->renameColumn('curriculum_id', 'sura_id');
        });

        Schema::table('page_logs', function (Blueprint $table) {
            $table->dropForeign(['curriculum_id']);
            $table->renameColumn('curriculum_id', 'sura_id');
        });

        Schema::table('memorizations', function (Blueprint $table) {
            $table->dropForeign(['curriculum_id']);
            $table->dropUnique(['student_id', 'curriculum_id']);
            $table->renameColumn('curriculum_id', 'sura_id');
        });

        Schema::table('memorizations', function (Blueprint $table) {
            $table->foreign('sura_id')->references('id')->on('suras')->cascadeOnDelete();
            $table->unique(['student_id', 'sura_id']);
        });
    }
};
