# Goal Description

Develop the "Taj Al-Ridha" (تاج الرضا) system for managing Quran memorization circles. The project will be built uniquely using the **TALL Stack (Tailwind, Alpine.js, Laravel, Livewire) inside a Filament environment**. The main goals are to have a very fast, responsive interface for teachers to manage students' attendance and recitations, and a comprehensive admin panel.

## User Review Required

> [!IMPORTANT]
> Since this is a brand new project, please review the proposed Database Schema and Models before I start generating the code. Let me know if you would like me to use Spatie Laravel Permission for user roles, or stick to a simple `role` string column (`admin` / `teacher`) on the users table. I will default to a simple `role` enum/string column as indicated in [DB.md](file:///home/hcody/Documents/Laravel%20Projects/taj_rida/DB.md) unless specified otherwise.
> Also note we will be using `laravel-boost` guidelines as per your [GEMINI.md](file:///home/hcody/Documents/Laravel%20Projects/taj_rida/GEMINI.md).

## Proposed Changes

### Database & Models

I will create migrations, models, factories, and seeders for the following entities:

#### [NEW] `create_students_table.php` / `Student.php`
- Fields: `teacher_id` (FK), `name`, `birthdate` (replacing `age` as per DB.md), `points_multiplier` (float default 1.0), `father_name`, `father_phone`, `more_details`, `notes`.
#### [NEW] `create_suras_table.php` / `Sura.php`
- Fields: `name`, `ayas_count`. (Seeded with 114 Suras).
#### [NEW] `create_recitations_table.php` / `Recitation.php`
- Fields: `student_id` (FK), `sura_id` (FK), `from_aya`, `to_aya`, `grade`, `date`.
#### [NEW] `create_revisions_table.php` / `Revision.php`
- Fields: Same as `Recitation`.
#### [NEW] `create_page_logs_table.php` / `PageLog.php`
- Fields: `student_id` (FK), `type` (recitation/revision), `count` (float), `date`.
#### [NEW] `create_attendances_table.php` / `Attendance.php`
- Fields: `student_id` (FK), `date`, `is_present` (boolean).
#### [NEW] `create_point_transactions_table.php` / `PointTransaction.php`
- Fields: `student_id` (FK), `teacher_id` (FK), `amount`, `reason`.
#### [NEW] `create_student_notes_table.php` / `StudentNote.php`
- Fields: `student_id` (FK), `description`, `rating` (1-10), `date`.
#### [NEW] `create_settings_table.php` / `Setting.php`
- Fields: `key`, `value`. (Seeded with required keys for point calculation multipliers).

---

### Business Logic (Observers)

#### [NEW] `PageLogObserver.php`
- Listens to `created` / `updated` / `deleted` events on `PageLog` to calculate points based on `Settings` (e.g. `recitation_points_per_page`) multiplied by `Student->points_multiplier` and automatically create/modify a `PointTransaction`.
#### [NEW] `AttendanceObserver.php`
- Listens to `Attendance` changes to add or subtract points according to `attendance_points` or `absence_penalty` from `Settings`.
#### [MODIFY] `Student.php`
- Add a `GlobalScope` so that teachers only see students assigned to them (`where('teacher_id', auth()->id())`). Admins see all.

---

### Filament Resources

#### [NEW] `StudentResource.php`
- To manage student profiles. Will include extensive RelationManagers to view history of points, recitations, revisions, and attendance.
#### [NEW] `SettingResource.php` (Admin Only)
- Manage dynamic settings for points values.
#### [NEW] `UserResource.php` (Admin Only)
- To manage Teachers / Admins.
#### [NEW] `SuraResource.php` (Admin Only / Read-only)

---

### Custom Teacher Dashboard (TALL Stack)

#### [NEW] `TeacherDashboard.php` (Filament Page)
- Provide a custom Filament page specifically designed for Teachers.
#### [NEW] `app/Livewire/TeacherStudentCard.php`
- A specific Livewire component inside the Dashboard to render students as cards.
- **Features:** 
  - Toggle Attendance quickly.
  - Quick `+` or `-` buttons for recitation/revision pages (adjusts `PageLog` directly).
  - Modal button for manual 'Grant Points'.
  - Modal button for manual 'Deduct Points'.

## Verification Plan

### Automated Tests
- Pest unit tests checking the `PageLogObserver` logic to ensure points are correctly derived from settings.
- Feature tests to verify Teacher authorization (they can't modify unassigned students).

### Manual Verification
- We will login as a Teacher, navigate to the Custom Dashboard, click the attendance toggle to verify reactivity, click '+' to verify live point updates on the card.
