<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * One-time data migration: Step 3.
 *
 * For each teacher (role = teacher) who does not yet have a default classroom:
 *   - If a classroom is already linked (but not marked default), mark it default.
 *   - Otherwise, create a new classroom inside the active dawara and link it.
 *   - Set classroom_id on all students still belonging to this teacher (via teacher_id)
 *     that do not yet have a classroom.
 *
 * Safe to run more than once — skips any teacher that already has a default classroom.
 */
return new class extends Migration
{
    public function up(): void
    {
        // Resolve the active dawara id (nullable — classrooms can exist without one).
        $dawaraId = DB::table('dawaras')->where('status', 'active')->value('id');

        $teachers = DB::table('users')->where('role', 'teacher')->get();

        foreach ($teachers as $teacher) {
            // Skip teachers that already have a default classroom assigned.
            $hasDefault = DB::table('classroom_users')
                ->where('user_id', $teacher->id)
                ->where('is_default', true)
                ->exists();

            if ($hasDefault) {
                continue;
            }

            // Check if a non-default classroom link already exists for this teacher.
            $existingLink = DB::table('classroom_users')
                ->where('user_id', $teacher->id)
                ->first();

            if ($existingLink) {
                // Just promote the existing link to default.
                DB::table('classroom_users')
                    ->where('id', $existingLink->id)
                    ->update(['is_default' => true, 'updated_at' => now()]);

                $classroomId = $existingLink->classroom_id;
            } else {
                // Create a brand-new classroom for this teacher.
                $classroomId = DB::table('classrooms')->insertGetId([
                    'dawara_id' => $dawaraId,
                    'name' => 'حلقة '.$teacher->name,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                DB::table('classroom_users')->insert([
                    'classroom_id' => $classroomId,
                    'user_id' => $teacher->id,
                    'is_default' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Assign all of this teacher's students that have no classroom yet.
            DB::table('students')
                ->where('teacher_id', $teacher->id)
                ->whereNull('classroom_id')
                ->update(['classroom_id' => $classroomId, 'updated_at' => now()]);
        }
    }

    /** Data-only migration — no safe rollback. */
    public function down(): void {}
};
