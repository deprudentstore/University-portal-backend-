<?php

namespace App\Services;

class GradeService
{
    public function calculateGPA($grades)
    {
        $totalPoints = 0;
        $totalCredits = 0;

        foreach ($grades as $grade) {
            $creditHours = $grade->enrollment->course->credit_hours ?? 3;
            $points = $this->gradeToPoints($grade->letter_grade ?? 'F');
            $totalPoints += $points * $creditHours;
            $totalCredits += $creditHours;
        }

        return $totalCredits > 0 ? round($totalPoints / $totalCredits, 2) : 0.0;
    }

    public function calculateCourseGPA($score)
    {
        if ($score >= 90) return 4.0;
        if ($score >= 80) return 3.5;
        if ($score >= 70) return 3.0;
        if ($score >= 60) return 2.5;
        if ($score >= 50) return 2.0;
        if ($score >= 40) return 1.0;
        return 0.0;
    }

    public function calculateLetterGrade($score)
    {
        if ($score >= 90) return 'A';
        if ($score >= 80) return 'B+';
        if ($score >= 70) return 'B';
        if ($score >= 60) return 'C+';
        if ($score >= 50) return 'C';
        if ($score >= 40) return 'D';
        return 'F';
    }

    private function gradeToPoints($letter)
    {
        $map = ['A'=>4.0, 'B+'=>3.5, 'B'=>3.0, 'C+'=>2.5, 'C'=>2.0, 'D'=>1.0, 'F'=>0.0];
        return $map[$letter] ?? 0.0;
    }
}