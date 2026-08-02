<!DOCTYPE html>
<html>
<head><title>Transcript</title>
<style>body{font-family:sans-serif;}table{width:100%;border-collapse:collapse;}th,td{border:1px solid #000;padding:8px;}</style>
</head>
<body>
<h2>Transcript for {{ $user->name }}</h2>
<p>Email: {{ $user->email }}</p>
<table>
    <thead><tr><th>Course</th><th>Score</th><th>Grade</th></tr></thead>
    <tbody>
    @foreach($grades as $grade)
    <tr>
        <td>{{ $grade->enrollment->course->title }}</td>
        <td>{{ $grade->score }}</td>
        <td>{{ $grade->letter_grade }}</td>
    </tr>
    @endforeach
    </tbody>
</table>
<p><strong>GPA: {{ $gpa }}</strong></p>
</body>
</html>