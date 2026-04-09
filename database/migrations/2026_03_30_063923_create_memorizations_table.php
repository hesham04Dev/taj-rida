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
        Schema::create('memorizations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('sura_id')->constrained()->cascadeOnDelete();
            $table->float('memorized_pages')->default(0); 
            // NOTE WHILE THE MEMORIZATION PAGES LESS THAN TOTAL PAGES DONT INCREAZE THE REPETION
            $table->string('memorization_degree')->nullable();
            $table->integer('memorization_repetition')->default(0);
            $table->string('revision_degree')->nullable();
            $table->integer('revision_repetition')->default(0);
            $table->unique(['student_id', 'sura_id']);
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('memorizations');
    }
};
