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
            $table->boolean('is_need_revision')->default(false)->after('is_need_rememorisation');
            $table->unsignedSmallInteger('need_from_page')->nullable()->after('is_need_revision');
            $table->unsignedSmallInteger('need_to_page')->nullable()->after('need_from_page');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('memorizations', function (Blueprint $table) {
            $table->dropColumn(['is_need_revision', 'need_from_page', 'need_to_page']);
        });
    }
};
