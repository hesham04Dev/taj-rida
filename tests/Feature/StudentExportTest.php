<?php

use App\Filament\Resources\Students\Pages\ListStudents;
use App\Models\Attendance;
use App\Models\Memorization;
use App\Models\Student;
use App\Models\Sura;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

it('can export student data as csv', function () {
    $teacher = User::factory()->create(['role' => 'teacher']);
    $this->actingAs($teacher);

    // Create some Suras
    $sura1 = Sura::factory()->create(['id' => 1, 'name' => 'الفاتحة', 'pages_count' => 1.0]);
    $sura2 = Sura::factory()->create(['id' => 2, 'name' => 'البقرة', 'pages_count' => 48.0]);

    // Create a student for this teacher
    $student = Student::factory()->create([
        'teacher_id' => $teacher->id,
        'name' => 'محمد أحمد',
    ]);

    // Create attendances
    Attendance::factory()->create([
        'student_id' => $student->id,
        'is_present' => true,
        'date' => now()->subDays(2),
    ]);
    Attendance::factory()->create([
        'student_id' => $student->id,
        'is_present' => true,
        'date' => now()->subDays(1),
    ]);
    Attendance::factory()->create([
        'student_id' => $student->id,
        'is_present' => false,
        'date' => now(),
    ]);

    // Create memorizations
    // Student memorized 1 page of Sura 1 (100%) and 12 pages of Sura 2 (25%)
    Memorization::create([
        'student_id' => $student->id,
        'sura_id' => $sura1->id,
        'memorized_pages' => 1.0,
    ]);
    Memorization::create([
        'student_id' => $student->id,
        'sura_id' => $sura2->id,
        'memorized_pages' => 12.0,
    ]);

    // Call the export action
    $filename = 'students_export_'.now()->format('Y-m-d').'.csv';

    $response = Livewire::test(ListStudents::class)
        ->callAction('export_data');

    $response->assertSuccessful();

    $expectedContent = "\xEF\xBB\xBF"
        ."\"اسم الطالب\",\"عدد الحضور\",\"عدد الغياب\",\"إجمالي الصفحات المحفوظة\",الفاتحة,البقرة\n"
        ."\"محمد أحمد\",2,1,13,100%,25%\n";

    $response->assertFileDownloaded($filename, $expectedContent);
});
