<?php

use App\Models\Conversation;
use App\Models\Guardian;
use App\Models\Message;
use App\Models\Student;
use App\Models\User;

it('conversation is created for a teacher-guardian-student triplet', function () {
    $teacher = User::factory()->create();
    $guardian = Guardian::factory()->create();
    $student = Student::factory()->create(['teacher_id' => $teacher->id]);

    $conversation = Conversation::firstOrCreateFor($student->id, $teacher->id, $guardian->id);

    expect($conversation)->toBeInstanceOf(Conversation::class);
    expect($conversation->student_id)->toBe($student->id);
    expect($conversation->teacher_id)->toBe($teacher->id);
    expect($conversation->guardian_id)->toBe($guardian->id);
});

it('firstOrCreateFor returns same conversation on duplicate call', function () {
    $teacher = User::factory()->create();
    $guardian = Guardian::factory()->create();
    $student = Student::factory()->create(['teacher_id' => $teacher->id]);

    $conv1 = Conversation::firstOrCreateFor($student->id, $teacher->id, $guardian->id);
    $conv2 = Conversation::firstOrCreateFor($student->id, $teacher->id, $guardian->id);

    expect($conv1->id)->toBe($conv2->id);
    expect(Conversation::count())->toBe(1);
});

it('teacher can send a message in a conversation', function () {
    $teacher = User::factory()->create();
    $guardian = Guardian::factory()->create();
    $student = Student::factory()->create(['teacher_id' => $teacher->id]);

    $conversation = Conversation::firstOrCreateFor($student->id, $teacher->id, $guardian->id);

    $message = $conversation->messages()->create([
        'sender_type' => 'teacher',
        'sender_id' => $teacher->id,
        'body' => 'أداء الطالب رائع هذا الأسبوع',
    ]);

    expect($message->isFromTeacher())->toBeTrue();
    expect($message->isFromGuardian())->toBeFalse();
    expect($conversation->messages()->count())->toBe(1);
});

it('guardian can send a message in a conversation', function () {
    $teacher = User::factory()->create();
    $guardian = Guardian::factory()->create();
    $student = Student::factory()->create(['teacher_id' => $teacher->id]);

    $conversation = Conversation::firstOrCreateFor($student->id, $teacher->id, $guardian->id);

    $message = $conversation->messages()->create([
        'sender_type' => 'guardian',
        'sender_id' => $guardian->id,
        'body' => 'شكراً جزيلاً على الاهتمام',
    ]);

    expect($message->isFromGuardian())->toBeTrue();
    expect($message->isFromTeacher())->toBeFalse();
});

it('unread count is correct before and after teacher reads guardian messages', function () {
    $teacher = User::factory()->create();
    $guardian = Guardian::factory()->create();
    $student = Student::factory()->create(['teacher_id' => $teacher->id]);

    $conversation = Conversation::firstOrCreateFor($student->id, $teacher->id, $guardian->id);

    // Guardian sends 2 messages
    $conversation->messages()->create(['sender_type' => 'guardian', 'sender_id' => $guardian->id, 'body' => 'رسالة 1']);
    $conversation->messages()->create(['sender_type' => 'guardian', 'sender_id' => $guardian->id, 'body' => 'رسالة 2']);

    $unreadBefore = Message::whereHas('conversation', fn ($q) => $q->where('teacher_id', $teacher->id))
        ->where('sender_type', 'guardian')
        ->whereNull('read_at')
        ->count();

    expect($unreadBefore)->toBe(2);

    // Teacher reads the messages
    $conversation->messages()
        ->where('sender_type', 'guardian')
        ->whereNull('read_at')
        ->update(['read_at' => now()]);

    $unreadAfter = Message::whereHas('conversation', fn ($q) => $q->where('teacher_id', $teacher->id))
        ->where('sender_type', 'guardian')
        ->whereNull('read_at')
        ->count();

    expect($unreadAfter)->toBe(0);
});

it('teacher cannot access another teacher\'s conversation', function () {
    $teacher1 = User::factory()->create();
    $teacher2 = User::factory()->create();
    $guardian = Guardian::factory()->create();
    $student = Student::factory()->create(['teacher_id' => $teacher1->id]);

    $conversation = Conversation::firstOrCreateFor($student->id, $teacher1->id, $guardian->id);

    // teacher2 should not find teacher1's conversation
    $found = Conversation::where('id', $conversation->id)
        ->where('teacher_id', $teacher2->id)
        ->first();

    expect($found)->toBeNull();
});
