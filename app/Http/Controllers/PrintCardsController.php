<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;

class PrintCardsController extends Controller
{
    public function __invoke(Request $request)
    {
        /** @var User $user */
        $user = auth()->user();

        $query = Student::with(['pointTransactions', 'teacher']);

        // Filter by teacher_id for non-admins, or by explicit teacher_id param for admins
        if ($user->role !== 'admin') {
            $query->where('teacher_id', $user->id);
        } elseif ($request->filled('teacher_id')) {
            $query->where('teacher_id', $request->teacher_id);
        }

        $students = $query
            ->get()
            ->map(function (Student $student) {
                $student->total_pts = (int) $student->pointTransactions->sum('amount');

                return $student;
            })
            ->sortByDesc('total_pts')
            ->values();

        $teacherName = null;
        if ($user->role !== 'admin') {
            $teacherName = $user->name;
        } elseif ($request->filled('teacher_id') && $students->isNotEmpty()) {
            $teacherName = $students->first()->teacher?->name;
        }

        return view('print-cards', compact('students', 'teacherName'));
    }
}
