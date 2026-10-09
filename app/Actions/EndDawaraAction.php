<?php

namespace App\Actions;

use App\Models\Dawara;
use App\Models\PointTransaction;
use Illuminate\Support\Facades\DB;

class EndDawaraAction
{
    /**
     * Ends the current active dawara, tallies up the points, and starts a new one.
     *
     * @return Dawara The new active dawara
     *
     * @throws \Exception
     */
    public function execute(): Dawara
    {
        return DB::transaction(function () {
            // 1. Lock the active dawara row for update
            $currentDawara = Dawara::active()->lockForUpdate()->first();

            if (! $currentDawara) {
                throw new \Exception('No active Dawara found to end.');
            }

            // 2. Calculate total points per student for this dawara
            // We bypass the global scope explicitly, even though we filter by dawara_id, just to be safe.
            $pointsAggregates = PointTransaction::withoutGlobalScope('current_dawara')
                ->where('dawara_id', $currentDawara->id)
                ->select('student_id', DB::raw('SUM(amount) as total_points'))
                ->groupBy('student_id')
                ->get();

            $pivotData = [];
            foreach ($pointsAggregates as $aggregate) {
                // 3. Compute final_grade via an injectable or replaceable method
                $finalGrade = $this->calculateFinalGrade($aggregate->total_points);

                $pivotData[] = [
                    'dawara_id' => $currentDawara->id,
                    'student_id' => $aggregate->student_id,
                    'total_points' => $aggregate->total_points,
                    'final_grade' => $finalGrade,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            // Upsert into dawara_student pivot
            if (! empty($pivotData)) {
                DB::table('dawara_student')->upsert(
                    $pivotData,
                    ['dawara_id', 'student_id'], // Unique constraints to match on
                    ['total_points', 'final_grade', 'updated_at'] // Columns to update on duplicate
                );
            }

            // 4. Mark dawara completed and set end_date
            $currentDawara->update([
                'status' => Dawara::STATUS_COMPLETED,
                'end_date' => now(),
            ]);

            // 5. Create the next active dawara
            $count = Dawara::count();
            $newDawaraName = 'Dawara '.($count + 1);

            $newDawara = Dawara::create([
                'name' => $newDawaraName,
                'status' => Dawara::STATUS_ACTIVE,
                'start_date' => now(),
            ]);

            // Clear the cache so Dawara::current() pulls the new one
            Dawara::clearCurrentCache();

            return $newDawara;
        });
    }

    /**
     * Calculates the final grade based on total points.
     */
    protected function calculateFinalGrade(int $totalPoints): ?string
    {
        // TODO: Implement business rules for grading here.
        // For example, return 'A' if points > 100, etc.
        // For now, we return null to allow manual entry or further calculation.
        return null;
    }
}
