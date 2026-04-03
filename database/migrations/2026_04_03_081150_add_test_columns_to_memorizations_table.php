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
        Schema::table('memorizations', function (Blueprint $table) {
            $table->string('test_grade')->nullable();
            $table->integer('test_counts')->default(0);
            $table->string('last_test_name')->nullable();
            $table->boolean('is_need_rememorisation')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('memorizations', function (Blueprint $table) {
            $table->dropColumn([
                'test_grade',
                'test_counts',
                'last_test_name',
                'update_date',
                'is_need_rememorisation',
            ]);
        });
    }
};
