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
            $table->integer('memorized_ayas')->default(0);
            $table->string('memorization_degree')->nullable();
            $table->integer('memorization_repetition')->default(0);
            $table->string('revision_degree')->nullable();
            $table->integer('revision_repetition')->default(0);
            $table->unique(['student_id', 'sura_id']);
            $table->timestamps();
        });

        // Migrate existing recitations data
        $recitations = DB::table('recitations')
            ->select('student_id', 'sura_id', 'grade', 'to_aya', 'date')
            ->orderBy('date', 'asc')
            ->get();

        $recitationGroups = [];
        foreach ($recitations as $row) {
            $key = $row->student_id.'_'.$row->sura_id;
            if (! isset($recitationGroups[$key])) {
                $recitationGroups[$key] = [
                    'student_id' => $row->student_id,
                    'sura_id' => $row->sura_id,
                    'memorized_ayas' => $row->to_aya,
                    'memorization_degree' => $row->grade,
                    'memorization_repetition' => 0,
                ];
            }
            $recitationGroups[$key]['memorization_repetition']++;
            $recitationGroups[$key]['memorization_degree'] = $row->grade;
            $recitationGroups[$key]['memorized_ayas'] = $row->to_aya;
        }

        foreach ($recitationGroups as $data) {
            DB::table('memorizations')->upsert(
                [
                    'student_id' => $data['student_id'],
                    'sura_id' => $data['sura_id'],
                    'memorized_ayas' => $data['memorized_ayas'],
                    'memorization_degree' => $data['memorization_degree'],
                    'memorization_repetition' => $data['memorization_repetition'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                ['student_id', 'sura_id'],
                ['memorized_ayas', 'memorization_degree', 'memorization_repetition', 'updated_at']
            );
        }

        // Migrate existing revisions data
        $revisions = DB::table('revisions')
            ->select('student_id', 'sura_id', 'grade', 'date')
            ->orderBy('date', 'asc')
            ->get();

        $revisionGroups = [];
        foreach ($revisions as $row) {
            $key = $row->student_id.'_'.$row->sura_id;
            if (! isset($revisionGroups[$key])) {
                $revisionGroups[$key] = [
                    'student_id' => $row->student_id,
                    'sura_id' => $row->sura_id,
                    'revision_degree' => $row->grade,
                    'revision_repetition' => 0,
                ];
            }
            $revisionGroups[$key]['revision_repetition']++;
            $revisionGroups[$key]['revision_degree'] = $row->grade;
        }

        foreach ($revisionGroups as $data) {
            DB::table('memorizations')->upsert(
                [
                    'student_id' => $data['student_id'],
                    'sura_id' => $data['sura_id'],
                    'memorized_ayas' => 0,
                    'revision_degree' => $data['revision_degree'],
                    'revision_repetition' => $data['revision_repetition'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                ['student_id', 'sura_id'],
                ['revision_degree', 'revision_repetition', 'updated_at']
            );
        }

        Schema::dropIfExists('revisions');
        Schema::dropIfExists('recitations');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('memorizations');
    }
};
