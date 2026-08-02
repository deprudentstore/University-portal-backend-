<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Attendance;
use App\Models\Grade;
use App\Models\Enrollment;
use App\Services\GradeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FacultyController extends Controller
{
    protected $gradeService;

    public function __construct(GradeService $gradeService)
    {
        $this->gradeService = $gradeService;
    }

    public function courses()
    {
        $user = auth()->user();
        $courses = Course::where('instructor_id', $user->id)
                         ->withCount('enrollments')
                         ->get();
        return response()->json(['courses' => $courses]);
    }

    public function markAttendance(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'course_id' => 'required|exists:courses,id',
            'date' => 'required|date',
            'students' => 'required|array',
            'students.*.enrollment_id' => 'required|exists:enrollments,id',
            'students.*.status' => 'required|in:present,absent,excused'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $markedBy = auth()->id();
        $attendanceData = [];

        foreach ($request->students as $student) {
            $attendanceData[] = [
                'enrollment_id' => $student['enrollment_id'],
                'date' => $request->date,
                'status' => $student['status'],
                'marked_by' => $markedBy,
                'created_at' => now(),
                'updated_at' => now()
            ];
        }

        Attendance::upsert($attendanceData, ['enrollment_id', 'date'], ['status', 'marked_by']);

        return response()->json(['message' => 'Attendance saved successfully']);
    }

    public function enterGrade(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'enrollment_id' => 'required|exists:enrollments,id',
            'score' => 'required|numeric|min:0|max:100'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $enrollment = Enrollment::find($request->enrollment_id);
        $score = $request->score;
        $letter = $this->gradeService->calculateLetterGrade($score);
        $gpa = $this->gradeService->calculateCourseGPA($score);

        $grade = Grade::updateOrCreate(
            ['enrollment_id' => $request->enrollment_id],
            [
                'score' => $score,
                'letter_grade' => $letter,
                'gpa' => $gpa,
                'graded_by' => auth()->id()
            ]
        );

        return response()->json(['message' => 'Grade entered successfully', 'grade' => $grade]);
    }
}