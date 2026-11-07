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
        Schema::create('student_classroom', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->restrictOnDelete();
            $table->foreignId('classroom_id')->constrained('classrooms')->restrictOnDelete();
            $table->boolean('is_current')->default(true);
            $table->timestamps();
            $table->index(['student_id', 'is_current']);
        });

        // backfill: current class of every existing student
        // DB::table('students')->whereNotNull('classroom_id')->orderBy('id')
        //     ->each(function ($s) {
        //         DB::table('student_classroom')->insert([
        //             'student_id' => $s->id,
        //             'classroom_id' => $s->classroom_id,
        //             'started_at' => now()->toDateString(),
        //             'is_current' => true,
        //             'created_at' => now(), 'updated_at' => now(),
        //         ]);
        //     });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('classroom_student');
    }
};
