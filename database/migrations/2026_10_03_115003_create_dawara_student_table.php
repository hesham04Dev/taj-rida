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
        Schema::create('dawara_student', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dawara_id')->constrained('dawaras')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->integer('total_points')->default(0);
            $table->string('final_grade')->nullable();
            $table->text('summary_notes')->nullable();
            $table->timestamps();

            $table->unique(['dawara_id', 'student_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dawara_student');
    }
};
