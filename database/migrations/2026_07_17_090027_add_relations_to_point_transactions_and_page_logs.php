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
        Schema::table('point_transactions', function (Blueprint $table) {
            $table->foreignId('sura_id')->nullable()->constrained('suras')->nullOnDelete();
            $table->foreignId('page_log_id')->nullable()->constrained('page_logs')->cascadeOnDelete();
        });

        Schema::table('page_logs', function (Blueprint $table) {
            $table->foreignId('sura_id')->nullable()->constrained('suras')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('point_transactions', function (Blueprint $table) {
            $table->dropForeign(['point_transactions_sura_id_foreign']);
            $table->dropColumn('sura_id');
            $table->dropForeign(['point_transactions_page_log_id_foreign']);
            $table->dropColumn('page_log_id');
        });

        Schema::table('page_logs', function (Blueprint $table) {
            $table->dropForeign(['page_logs_sura_id_foreign']);
            $table->dropColumn('sura_id');
        });
    }
};
