<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Grade;
use App\Models\Attendance;
use App\Models\Fee;
use App\Models\Enrollment;
use App\Services\GradeService;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class StudentController extends Controller
{
    protected $gradeService;

    public function __construct(GradeService $gradeService)
    {
        $this->gradeService = $gradeService;
    }

    public function grades()
    {
        $user = auth()->user();
        $grades = Grade::whereHas('enrollment', function($q) use ($user) {
            $q->where('student_id', $user->id);
        })->with('enrollment.course')->get();

        $gpa = $this->gradeService->calculateGPA($grades);

        return response()->json([
            'grades' => $grades,
            'gpa' => $gpa
        ]);
    }

    public function attendance()
    {
        $user = auth()->user();
        $attendance = Attendance::whereHas('enrollment', function($q) use ($user) {
            $q->where('student_id', $user->id);
        })->with('enrollment.course')->get();

        $total = $attendance->count();
        $present = $attendance->where('status', 'present')->count();
        $percentage = $total > 0 ? round(($present / $total) * 100) : 0;

        return response()->json([
            'attendance' => $attendance,
            'percentage' => $percentage
        ]);
    }

    public function fees()
    {
        $user = auth()->user();
        $fees = Fee::where('student_id', $user->id)->orderBy('created_at', 'desc')->get();
        return response()->json(['fees' => $fees]);
    }

    public function transcript()
    {
        $user = auth()->user();
        $grades = Grade::whereHas('enrollment', function($q) use ($user) {
            $q->where('student_id', $user->id);
        })->with('enrollment.course')->get();

        $gpa = $this->gradeService->calculateGPA($grades);

        $pdf = Pdf::loadView('transcript', compact('user', 'grades', 'gpa'));
        return $pdf->download('transcript.pdf');
    }
}