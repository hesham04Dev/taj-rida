<?php

use App\Livewire\Parent\Dashboard;
use App\Models\Guardian;
use App\Models\Student;
use App\Models\StudentGuardian;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;

use function Pest\Laravel\get;

uses(RefreshDatabase::class);

function createParentWithStudents(string $phone = '0599000000', int $studentCount = 1): array
{
    $teacher = User::factory()->create(['role' => 'teacher']);
    $parent = Guardian::create([
        'name' => 'أبو أحمد',
        'phone' => $phone,
        'password' => Hash::make('password123'),
    ]);

    $students = [];
    for ($i = 0; $i < $studentCount; $i++) {
        $student = Student::factory()->create([
            'teacher_id' => $teacher->id,
            'name' => 'الابن '.($i + 1),
        ]);

        StudentGuardian::create([
            'student_id' => $student->id,
            'phone' => $phone,
            'name' => 'أبو أحمد',
            'relationship' => 'father',
        ]);

        $students[] = $student;
    }

    return [$parent, $students];
}

it('loads parent dashboard and displays child progress', function (): void {
    [$parent, $students] = createParentWithStudents('0599000000', 1);
    Auth::guard('guardian')->login($parent);

    get(route('parent.dashboard'))
        ->assertOk()
        ->assertSee('متابعة الابن:')
        ->assertSee($students[0]->name)
        ->assertSeeLivewire(Dashboard::class);
});

it('renders multi-child selector when parent has multiple siblings', function (): void {
    [$parent, $students] = createParentWithStudents('0599000000', 2);
    Auth::guard('guardian')->login($parent);

    get(route('parent.dashboard'))
        ->assertOk()
        ->assertSee('اختر الابن للمتابعة')
        ->assertSee($students[0]->name)
        ->assertSee($students[1]->name);
});

it('allows switching reactively between siblings', function (): void {
    [$parent, $students] = createParentWithStudents('0599000000', 2);
    Auth::guard('guardian')->login($parent);

    Livewire::test(Dashboard::class)
        ->assertSet('selectedStudentId', $students[0]->id)
        ->call('selectStudent', $students[1]->id)
        ->assertSet('selectedStudentId', $students[1]->id);
});
