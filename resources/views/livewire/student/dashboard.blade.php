<div>
    {{-- Welcome banner --}}
    <div class="mb-8">
        <h1 class="text-2xl font-bold dark:text-white">
            أهلاً، <span class="text-emerald-400">{{ $student->name }}</span> 👋
        </h1>
        <p class="text-zinc-400 text-sm mt-1">{{ now()->translatedFormat('l، j F Y') }}</p>
    </div>

    {{-- Top row: Task + Points --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <livewire:student.memorization-task />
        <livewire:student.points-card />
    </div>

    {{-- Grades row --}}
    <div class="mb-6">
        <livewire:student.grades-card />
    </div>

    {{-- Bottom row: Attendance + Notes --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <livewire:student.attendance-log />
        <livewire:student.teacher-notes />
    </div>
</div>
