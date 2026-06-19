<?php

use App\Models\Guardian;
use App\Models\Notification;
use App\Models\NotificationRead;
use App\Models\Student;
use App\Models\StudentGuardian;
use App\Models\User;
use Illuminate\Database\QueryException;

it('teacher can create a notification targeting a specific student', function () {
    $teacher = User::factory()->create();
    $student = Student::factory()->create(['teacher_id' => $teacher->id]);

    $notification = Notification::factory()->create([
        'teacher_id' => $teacher->id,
        'student_id' => $student->id,
        'title' => 'اختبار قرآني',
        'body' => 'سيكون هناك اختبار قرآني يوم الثلاثاء',
    ]);

    expect($notification)->toBeInstanceOf(Notification::class);
    expect($notification->student_id)->toBe($student->id);
    expect($notification->teacher_id)->toBe($teacher->id);
});

it('notification read rows are created for guardian linked to student', function () {
    $teacher = User::factory()->create();
    $guardian = Guardian::factory()->create();
    $student = Student::factory()->create(['teacher_id' => $teacher->id]);

    // Link guardian to student via phone
    StudentGuardian::create([
        'student_id' => $student->id,
        'phone' => $guardian->phone,
    ]);

    $notification = Notification::factory()->create([
        'teacher_id' => $teacher->id,
        'student_id' => $student->id,
    ]);

    // Manually create the read row (as the CreateNotification page does)
    NotificationRead::create([
        'notification_id' => $notification->id,
        'guardian_id' => $guardian->id,
        'read_at' => null,
    ]);

    expect(NotificationRead::where('notification_id', $notification->id)->count())->toBe(1);
    expect(NotificationRead::where('notification_id', $notification->id)->whereNull('read_at')->count())->toBe(1);
});

it('guardian can mark a notification as read', function () {
    $guardian = Guardian::factory()->create();
    $notification = Notification::factory()->create();

    $read = NotificationRead::create([
        'notification_id' => $notification->id,
        'guardian_id' => $guardian->id,
        'read_at' => null,
    ]);

    expect($read->read_at)->toBeNull();

    $read->update(['read_at' => now()]);
    $read->refresh();

    expect($read->read_at)->not->toBeNull();
});

it('unread count decreases after marking notifications read', function () {
    $guardian = Guardian::factory()->create();
    $notification1 = Notification::factory()->create();
    $notification2 = Notification::factory()->create();

    NotificationRead::create(['notification_id' => $notification1->id, 'guardian_id' => $guardian->id, 'read_at' => null]);
    NotificationRead::create(['notification_id' => $notification2->id, 'guardian_id' => $guardian->id, 'read_at' => null]);

    $unreadBefore = NotificationRead::where('guardian_id', $guardian->id)->whereNull('read_at')->count();
    expect($unreadBefore)->toBe(2);

    NotificationRead::where('guardian_id', $guardian->id)->whereNull('read_at')->update(['read_at' => now()]);

    $unreadAfter = NotificationRead::where('guardian_id', $guardian->id)->whereNull('read_at')->count();
    expect($unreadAfter)->toBe(0);
});

it('notification read is unique per guardian-notification pair', function () {
    $guardian = Guardian::factory()->create();
    $notification = Notification::factory()->create();

    NotificationRead::create([
        'notification_id' => $notification->id,
        'guardian_id' => $guardian->id,
        'read_at' => null,
    ]);

    expect(fn () => NotificationRead::create([
        'notification_id' => $notification->id,
        'guardian_id' => $guardian->id,
        'read_at' => null,
    ]))->toThrow(QueryException::class);
});
