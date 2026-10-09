<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Idempotent: check if a legacy dawara already exists
        $legacyDawara = DB::table('dawaras')->where('name', 'Legacy Dawara')->first();

        if (! $legacyDawara) {
            // Find earliest date if available
            $earliestTransaction = DB::table('point_transactions')->orderBy('created_at', 'asc')->first();
            $startDate = $earliestTransaction ? $earliestTransaction->created_at : now();

            DB::table('dawaras')->insert([
                'name' => 'Legacy Dawara',
                'status' => 'active',
                'start_date' => $startDate,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('dawaras')->where('name', 'Legacy Dawara')->delete();
    }
};
