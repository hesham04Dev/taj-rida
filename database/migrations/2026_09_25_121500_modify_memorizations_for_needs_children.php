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
            $table->dropColumn(['need_from_page', 'need_to_page']);
            $table->json('needs_revision_children')->nullable()->after('is_need_revision');
            $table->json('needs_rememorisation_children')->nullable()->after('is_need_rememorisation');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('memorizations', function (Blueprint $table) {
            $table->dropColumn(['needs_revision_children', 'needs_rememorisation_children']);
            $table->unsignedSmallInteger('need_from_page')->nullable()->after('is_need_revision');
            $table->unsignedSmallInteger('need_to_page')->nullable()->after('need_from_page');
        });
    }
};
