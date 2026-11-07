<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Stores the classroom the teacher has selected as their active view.
            // Nullable so non-teacher users are unaffected.
            $table->foreignId('selected_classroom_id')
                ->nullable()
                ->after('id')
                ->constrained('classrooms')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('selected_classroom_id');
        });
    }
};
