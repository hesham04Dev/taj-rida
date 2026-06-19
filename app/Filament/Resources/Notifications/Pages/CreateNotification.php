<?php

namespace App\Filament\Resources\Notifications\Pages;

use App\Filament\Resources\Notifications\NotificationResource;
use App\Models\Guardian;
use App\Models\Student;
use App\Models\StudentGuardian;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CreateNotification extends CreateRecord
{
    protected static string $resource = NotificationResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['teacher_id'] = Auth::id();

        return $data;
    }

    /**
     * After the notification record is saved, create NotificationRead rows
     * for every relevant guardian so we can track read status per guardian.
     */
    protected function afterCreate(): void
    {
        $notification = $this->record;
        $teacherId = Auth::id();

        // Collect the phone numbers that have linked guardians
        if ($notification->student_id) {
            $phones = StudentGuardian::where('student_id', $notification->student_id)
                ->pluck('phone');
        } else {
            $studentIds = Student::where('teacher_id', $teacherId)->pluck('id');
            $phones = StudentGuardian::whereIn('student_id', $studentIds)
                ->pluck('phone');
        }

        $guardianIds = Guardian::whereIn('phone', $phones)->pluck('id')->unique();

        $now = now();
        $rows = $guardianIds->map(fn ($guardianId) => [
            'notification_id' => $notification->id,
            'guardian_id' => $guardianId,
            'read_at' => null,
            'created_at' => $now,
            'updated_at' => $now,
        ])->values()->all();

        if (! empty($rows)) {
            DB::table('notification_reads')->insertOrIgnore($rows);
        }
    }
}
