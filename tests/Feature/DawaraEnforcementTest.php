<?php

use App\Enums\MemorizationType;
use App\Filament\Resources\BudgetTransactions\Pages\ListBudgetTransactions;
use App\Filament\Widgets\GiftsAndExchangeRatesWidget;
use App\Models\Dawara;
use App\Models\Memorization;
use App\Models\PointTransaction;
use App\Models\Setting;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;

uses(RefreshDatabase::class);

describe('Single-active-Dawara enforcement', function () {
    it('deactivates all other dawaras when a new one is set to active', function () {
        // Legacy seeder creates a "Legacy Dawara" as active - complete it first
        Dawara::where('name', 'Legacy Dawara')->update(['status' => Dawara::STATUS_COMPLETED]);
        Dawara::clearCurrentCache();

        $d1 = Dawara::create(['name' => 'Dawara 1', 'status' => Dawara::STATUS_ACTIVE]);
        Dawara::clearCurrentCache();
        $d2 = Dawara::create(['name' => 'Dawara 2', 'status' => Dawara::STATUS_ACTIVE]);

        $d1->refresh();
        expect($d1->status)->toBe(Dawara::STATUS_COMPLETED);
        expect($d2->status)->toBe(Dawara::STATUS_ACTIVE);
    });

    it('ensures only one active dawara exists at a time', function () {
        Dawara::create(['name' => 'Dawara 1', 'status' => Dawara::STATUS_ACTIVE]);
        Dawara::create(['name' => 'Dawara 2', 'status' => Dawara::STATUS_ACTIVE]);
        Dawara::create(['name' => 'Dawara 3', 'status' => Dawara::STATUS_ACTIVE]);

        $activeCount = Dawara::where('status', Dawara::STATUS_ACTIVE)->count();
        expect($activeCount)->toBe(1);
    });

    it('returns null from current() when no active dawara exists', function () {
        DB::table('dawaras')->update(['status' => Dawara::STATUS_COMPLETED]);
        Dawara::clearCurrentCache();

        expect(Dawara::current())->toBeNull();
    });

    it('returns the active dawara from current()', function () {
        DB::table('dawaras')->update(['status' => Dawara::STATUS_COMPLETED]);
        $dawara = Dawara::create(['name' => 'Active Dawara', 'status' => Dawara::STATUS_ACTIVE]);
        Dawara::clearCurrentCache();

        $current = Dawara::current();
        expect($current)->not->toBeNull();
        expect($current->id)->toBe($dawara->id);
    });
});

describe('PointTransaction global scope with no active dawara', function () {
    it('returns empty results when no active dawara', function () {
        $teacher = User::factory()->create();
        $student = Student::factory()->create(['teacher_id' => $teacher->id]);

        // Use raw insert to bypass global scope (no active dawara needed for insert)
        $legacyId = DB::table('dawaras')->where('name', 'Legacy Dawara')->value('id');
        DB::table('point_transactions')->insert([
            'student_id' => $student->id,
            'teacher_id' => $teacher->id,
            'dawara_id' => $legacyId,
            'amount' => 50,
            'reason' => 'old',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Deactivate all dawaras
        DB::table('dawaras')->update(['status' => Dawara::STATUS_COMPLETED]);
        Dawara::clearCurrentCache();

        // Global scope should return 0 results (not crash)
        $count = PointTransaction::count();
        expect($count)->toBe(0);
    });
});

describe('Memorization across dawaras', function () {
    it('shows memorizations from all dawaras without global scope restriction', function () {
        $teacher = User::factory()->create();
        $student = Student::factory()->create(['teacher_id' => $teacher->id]);
        $curriculum = DB::table('curriculum')->insertGetId([
            'name' => 'Test Curriculum',
            'number' => 99,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $d1 = Dawara::where('name', 'Legacy Dawara')->first();
        Dawara::clearCurrentCache();

        DB::table('memorizations')->insert([
            'student_id' => $student->id,
            'curriculum_id' => $curriculum,
            'dawara_id' => $d1->id,
            'type' => 'regular',
            'memorized_pages' => 5,
            'memorization_repetition' => 1,
            'revision_repetition' => 0,
            'is_need_rememorisation' => false,
            'is_need_revision' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Complete d1 and create d2
        $d1->update(['status' => Dawara::STATUS_COMPLETED]);
        Dawara::create(['name' => 'Dawara 2', 'status' => Dawara::STATUS_ACTIVE]);
        Dawara::clearCurrentCache();

        // Memorizations are no longer scoped to current dawara
        $allMemorizations = Memorization::withoutGlobalScopes()->where('student_id', $student->id)->get();
        expect($allMemorizations)->toHaveCount(1);
        expect($allMemorizations->first()->dawara_id)->toBe($d1->id);
    });

    it('correctly identifies if a memorization is in the current dawara', function () {
        $d1 = Dawara::where('name', 'Legacy Dawara')->first();
        Dawara::clearCurrentCache();

        $mem = new Memorization([
            'student_id' => 999,
            'curriculum_id' => 999,
            'dawara_id' => $d1->id,
            'type' => 'regular',
            'memorized_pages' => 3,
            'memorization_repetition' => 0,
            'revision_repetition' => 0,
        ]);

        expect($mem->isInCurrentDawara())->toBeTrue();

        // Switch active dawara
        $d1->update(['status' => Dawara::STATUS_COMPLETED]);
        Dawara::create(['name' => 'Dawara B', 'status' => Dawara::STATUS_ACTIVE]);
        Dawara::clearCurrentCache();

        expect($mem->isInCurrentDawara())->toBeFalse();
    });

    it('hides points for memorizations outside the active dawara', function () {
        $teacher = User::factory()->create();
        $student = Student::factory()->create(['teacher_id' => $teacher->id]);
        $curriculum = DB::table('curriculum')->insertGetId([
            'name' => 'Al-Baqarah',
            'number' => 2,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $d1 = Dawara::where('name', 'Legacy Dawara')->first();
        Dawara::clearCurrentCache();

        $mem = Memorization::create([
            'student_id' => $student->id,
            'curriculum_id' => $curriculum,
            'dawara_id' => $d1->id,
            'type' => 'regular',
            'memorized_pages' => 10,
            'memorization_repetition' => 1,
            'revision_repetition' => 0,
            'is_need_rememorisation' => false,
            'is_need_revision' => false,
        ]);

        DB::table('point_transactions')->insert([
            'student_id' => $student->id,
            'teacher_id' => $teacher->id,
            'curriculum_id' => $curriculum,
            'dawara_id' => $d1->id,
            'amount' => 100,
            'reason' => 'test hifz',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // When d1 is active, points_earned should be visible
        expect($mem->points_earned)->toBe(100);
        expect($mem->toArray())->toHaveKey('points_earned');

        // Complete d1 and create active d2
        $d1->update(['status' => Dawara::STATUS_COMPLETED]);
        Dawara::create(['name' => 'Dawara 2', 'status' => Dawara::STATUS_ACTIVE]);
        Dawara::clearCurrentCache();

        // When d1 is completed and d2 is active, points_earned should be null and hidden from toArray
        expect($mem->points_earned)->toBeNull();
        expect($mem->toArray())->not->toHaveKey('points_earned');
    });
});

describe('RequireActiveDawara middleware redirection', function () {
    it('redirects user to dawaras resource when no active dawara exists', function () {
        $admin = User::factory()->create(['role' => 'admin']);
        DB::table('dawaras')->update(['status' => Dawara::STATUS_COMPLETED]);
        Dawara::clearCurrentCache();

        $this->actingAs($admin)
            ->get(route('filament.admin.resources.students.index'))
            ->assertRedirect(route('filament.admin.resources.dawaras.index'));
    });

    it('allows access to exempt routes when no active dawara exists', function () {
        $admin = User::factory()->create(['role' => 'admin']);
        DB::table('dawaras')->update(['status' => Dawara::STATUS_COMPLETED]);
        Dawara::clearCurrentCache();

        $this->actingAs($admin)
            ->get(route('filament.admin.resources.dawaras.index'))
            ->assertSuccessful();
    });

    it('allows access to dawara-dependent routes when active dawara exists', function () {
        $admin = User::factory()->create(['role' => 'admin']);
        Dawara::create(['name' => 'Live Dawara', 'status' => Dawara::STATUS_ACTIVE]);
        Dawara::clearCurrentCache();

        $this->actingAs($admin)
            ->get(route('filament.admin.resources.students.index'))
            ->assertSuccessful();
    });
});

describe('Exchange rate calculations and widget', function () {
    it('calculates actual exchange rate and handles division by zero', function () {
        DB::table('dawaras')->update(['status' => Dawara::STATUS_COMPLETED]);
        $dawara = Dawara::create(['name' => 'Finance Dawara', 'status' => Dawara::STATUS_ACTIVE]);
        Dawara::clearCurrentCache();

        // Insert gift budget transaction
        DB::table('budget_transactions')->insert([
            'type' => 'gift',
            'amount' => 500.00,
            'date' => now()->toDateString(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $admin = User::factory()->create(['role' => 'admin']);
        Setting::updateOrCreate(['key' => 'expected_exchange_rate'], ['value' => '0.25']);

        // Test widget instantiation and calculation
        $widget = new GiftsAndExchangeRatesWidget;
        $reflection = new ReflectionClass($widget);
        $method = $reflection->getMethod('getStats');
        $method->setAccessible(true);

        // Case 1: Division by zero when points are 0
        $stats = $method->invoke($widget);
        expect($stats[0]->getValue())->toContain('500.00');
        expect($stats[1]->getValue())->toContain('0.0000');
        expect($stats[2]->getValue())->toContain('0.2500');

        // Case 2: With points present
        $teacher = User::factory()->create();
        $student = Student::factory()->create(['teacher_id' => $teacher->id]);
        DB::table('point_transactions')->insert([
            'student_id' => $student->id,
            'teacher_id' => $teacher->id,
            'dawara_id' => $dawara->id,
            'amount' => 1000,
            'reason' => 'test points',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $statsWithPoints = $method->invoke($widget);
        // 500 / 1000 = 0.5000
        expect($statsWithPoints[1]->getValue())->toContain('0.5000');

        // Case 3: When gifts_balance is explicitly configured
        Setting::updateOrCreate(['key' => 'gifts_balance'], ['value' => '800']);
        $statsWithCustomGifts = $method->invoke($widget);
        expect($statsWithCustomGifts[0]->getValue())->toContain('800.00');
        // 800 / 1000 = 0.8000
        expect($statsWithCustomGifts[1]->getValue())->toContain('0.8000');
    });

    it('allows admin to set gifts balance and expected exchange rate from budget page action', function () {
        $admin = User::factory()->create(['role' => 'admin']);
        Dawara::create(['name' => 'Active Dawara', 'status' => Dawara::STATUS_ACTIVE]);
        Dawara::clearCurrentCache();

        Livewire::actingAs($admin)
            ->test(ListBudgetTransactions::class)
            ->callAction('manageGiftsAndRates', [
                'gifts_balance' => 1200,
                'expected_exchange_rate' => 0.15,
            ])
            ->assertHasNoActionErrors();

        expect(Setting::where('key', 'gifts_balance')->value('value'))->toBe('1200');
        expect(Setting::where('key', 'expected_exchange_rate')->value('value'))->toBe('0.15');
    });
});

describe('Memorization types including Serd', function () {
    it('supports Serd memorization type and defaults to regular', function () {
        $teacher = User::factory()->create();
        $student = Student::factory()->create(['teacher_id' => $teacher->id]);
        $curriculum1 = DB::table('curriculum')->insertGetId([
            'name' => 'Al-Ikhlas',
            'number' => 112,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $curriculum2 = DB::table('curriculum')->insertGetId([
            'name' => 'Al-Falaq',
            'number' => 113,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $dawara = Dawara::where('status', Dawara::STATUS_ACTIVE)->first();

        // Regular default
        $regularMem = Memorization::create([
            'student_id' => $student->id,
            'curriculum_id' => $curriculum1,
            'dawara_id' => $dawara->id,
        ]);
        expect($regularMem->type)->toBe(MemorizationType::Regular);

        // Serd type
        $serdMem = Memorization::create([
            'student_id' => $student->id,
            'curriculum_id' => $curriculum2,
            'dawara_id' => $dawara->id,
            'type' => MemorizationType::Serd,
        ]);
        expect($serdMem->type)->toBe(MemorizationType::Serd);
        expect($serdMem->type->label())->toBe('سرد');
    });
});
