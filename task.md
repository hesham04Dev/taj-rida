# Task List: TALL Stack Taj Al-Ridha System

## 1. Database Schema & Models
- [x] Update `users` table migration and [User](file:///home/hcody/Documents/Laravel%20Projects/taj_rida/app/Models/User.php#15-57) model (`role`, `phone`).
- [x] Create [Student](file:///home/hcody/Documents/Laravel%20Projects/taj_rida/app/Models/Student.php#9-66) model, migration, factory, and seeder.
- [x] Create [Sura](file:///home/hcody/Documents/Laravel%20Projects/taj_rida/app/Models/Sura.php#7-21) model, migration, and seeder.
- [x] Create [Recitation](file:///home/hcody/Documents/Laravel%20Projects/taj_rida/app/Models/Recitation.php#8-31) model, migration, and factory.
- [x] Create [Revision](file:///home/hcody/Documents/Laravel%20Projects/taj_rida/app/Models/Revision.php#8-31) model, migration, and factory.
- [x] Create [PageLog](file:///home/hcody/Documents/Laravel%20Projects/taj_rida/app/Models/PageLog.php#8-26) model, migration, and factory.
- [x] Create [Attendance](file:///home/hcody/Documents/Laravel%20Projects/taj_rida/app/Models/Attendance.php#8-27) model, migration, and factory.
- [x] Create [PointTransaction](file:///home/hcody/Documents/Laravel%20Projects/taj_rida/app/Models/PointTransaction.php#8-24) model, migration, and factory.
- [x] Create [StudentNote](file:///home/hcody/Documents/Laravel%20Projects/taj_rida/app/Models/StudentNote.php#8-26) model, migration, and factory.
- [x] Create [Setting](file:///home/hcody/Documents/Laravel%20Projects/taj_rida/app/Models/Setting.php#7-11) model, migration, and seeder.

## 2. Business Logic & Global Scopes
- [x] Implement `StudentGlobalScope` for teacher authorization.
- [x] Implement [PageLogObserver](file:///home/hcody/Documents/Laravel%20Projects/taj_rida/app/Observers/PageLogObserver.php#9-36) to trigger [PointTransaction](file:///home/hcody/Documents/Laravel%20Projects/taj_rida/app/Models/PointTransaction.php#8-24) creation based on `Settings`.
- [x] Implement [AttendanceObserver](file:///home/hcody/Documents/Laravel%20Projects/taj_rida/app/Observers/AttendanceObserver.php#9-39) to trigger points rules.
- [x] Create Model Policies to protect data based on User User-Role.

## 3. Filament Resources
- [x] [UserResource](file:///home/hcody/Documents/Laravel%20Projects/taj_rida/app/Filament/Resources/Users/UserResource.php#17-54) (Admin only).
- [x] [StudentResource](file:///home/hcody/Documents/Laravel%20Projects/taj_rida/app/Filament/Resources/Students/StudentResource.php#20-63) with Relation Managers.
    - [x] `RecitationsRelationManager`.
    - [x] `RevisionsRelationManager`.
    - [x] `PageLogsRelationManager`.
    - [x] `AttendancesRelationManager`.
    - [x] `PointTransactionsRelationManager`.
    - [x] `StudentNotesRelationManager`.
- [x] [SettingResource](file:///home/hcody/Documents/Laravel%20Projects/taj_rida/app/Filament/Resources/Settings/SettingResource.php#17-54) (Admin only).

## 4. Custom Teacher Dashboard (TALL Stack)
- [x] Create Custom Filament Page ([TeacherDashboard](file:///home/hcody/Documents/Laravel%20Projects/taj_rida/app/Filament/Pages/TeacherDashboard.php#7-23)).
- [x] Create Livewire Component ([StudentCard](file:///home/hcody/Documents/Laravel%20Projects/taj_rida/app/Livewire/StudentCard.php#11-71)) for quick actions.
    - [x] Toggle Attendance (Green/Red).
    - [x] +/- Buttons for Recitation/Revision Pages.
    - [x] Modals for manual Give/Deduct points.
- [x] Create Stats Overview Widgets (Pages this week, Leaderboard, Attendance Rates).
