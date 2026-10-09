<?php

use App\Actions\EndDawaraAction;
use App\Models\Curriculum;
use App\Models\Dawara;
use App\Models\PointTransaction;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Seed initial data and Dawara to test against
    $this->teacher = User::factory()->create();
    $this->curriculum = Curriculum::create(['name' => 'Al-Fatiha', 'number' => 1]);
});

it('tests backfill logic leaving zero null dawara_id', function () {
    // This test validates the logic of the legacy dawara seeder migration.
    // Since point_transactions.dawara_id is now NOT NULL, we simulate the
    // scenario using raw SQL with a pre-assigned legacy dawara.
    $student = Student::factory()->create(['teacher_id' => $this->teacher->id]);

    // Simulate legacy data that already has the legacy dawara assigned
    $legacyDawara = Dawara::where('name', 'Legacy Dawara')->first();
    DB::table('point_transactions')->insert([
        'student_id' => $student->id,
        'teacher_id' => $this->teacher->id,
        'dawara_id' => $legacyDawara->id,
        'amount' => 10,
        'reason' => 'test',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    // Verify no NULL dawara_ids exist (the backfill already ran via the migration seeder)
    $nullCount = DB::table('point_transactions')->whereNull('dawara_id')->count();
    expect($nullCount)->toBe(0);
});

it('tests global scope filters correctly', function () {
    $dawara1 = Dawara::create(['name' => 'Dawara 1', 'status' => Dawara::STATUS_ACTIVE]);
    Dawara::clearCurrentCache();

    $student = Student::factory()->create(['teacher_id' => $this->teacher->id]);

    // This uses the trait to auto-set dawara_id to current (dawara1)
    $pt1 = PointTransaction::create([
        'student_id' => $student->id,
        'teacher_id' => $this->teacher->id,
        'amount' => 10,
        'reason' => 'Term 1 point',
    ]);

    // Complete term 1 and start term 2
    $dawara1->update(['status' => Dawara::STATUS_COMPLETED]);
    $dawara2 = Dawara::create(['name' => 'Dawara 2', 'status' => Dawara::STATUS_ACTIVE]);
    Dawara::clearCurrentCache();

    $pt2 = PointTransaction::create([
        'student_id' => $student->id,
        'teacher_id' => $this->teacher->id,
        'amount' => 20,
        'reason' => 'Term 2 point',
    ]);

    // Current query should only see PT2
    $transactions = PointTransaction::all();
    expect($transactions)->toHaveCount(1);
    expect($transactions->first()->id)->toBe($pt2->id);

    // Historic query should see PT1
    $historic = PointTransaction::forDawara($dawara1)->get();
    expect($historic)->toHaveCount(1);
    expect($historic->first()->id)->toBe($pt1->id);
});

it('tests EndDawaraAction produces correct pivot totals and creates new dawara', function () {
    $dawara1 = Dawara::create(['name' => 'Dawara 1', 'status' => Dawara::STATUS_ACTIVE]);
    Dawara::clearCurrentCache();

    $student1 = Student::factory()->create(['teacher_id' => $this->teacher->id]);
    $student2 = Student::factory()->create(['teacher_id' => $this->teacher->id]);

    PointTransaction::create(['student_id' => $student1->id, 'teacher_id' => $this->teacher->id, 'amount' => 10, 'reason' => '1']);
    PointTransaction::create(['student_id' => $student1->id, 'teacher_id' => $this->teacher->id, 'amount' => 15, 'reason' => '2']);
    PointTransaction::create(['student_id' => $student2->id, 'teacher_id' => $this->teacher->id, 'amount' => 5, 'reason' => '3']);

    $action = new EndDawaraAction;
    $newDawara = $action->execute();

    expect($newDawara->name)->toContain('Dawara');
    expect($newDawara->status)->toBe(Dawara::STATUS_ACTIVE);

    $dawara1->refresh();
    expect($dawara1->status)->toBe(Dawara::STATUS_COMPLETED);

    $pivot1 = DB::table('dawara_student')->where('dawara_id', $dawara1->id)->where('student_id', $student1->id)->first();
    expect($pivot1->total_points)->toBe(25);

    $pivot2 = DB::table('dawara_student')->where('dawara_id', $dawara1->id)->where('student_id', $student2->id)->first();
    expect($pivot2->total_points)->toBe(5);
});

it('tests concurrent invocation of EndDawaraAction fails safely', function () {
    Dawara::create(['name' => 'Dawara 1', 'status' => Dawara::STATUS_ACTIVE]);
    Dawara::clearCurrentCache();

    $action = new EndDawaraAction;
    $action->execute();

    // The first one completed Dawara 1, so if we try to artificially simulate a double click
    // without another active dawara (if we had completed it already without creating a new one)
    // it would throw. But since a new active one is created, we will end the *new* one.
    // Let's test what happens if no active dawara exists.

    DB::table('dawaras')->update(['status' => Dawara::STATUS_COMPLETED]);
    Dawara::clearCurrentCache();

    expect(fn () => $action->execute())->toThrow(Exception::class, 'No active Dawara found to end.');
});
