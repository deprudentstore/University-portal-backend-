<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Department;
use App\Models\Course;
use App\Models\Book;
use App\Models\Notice;
use App\Models\Enrollment;
use App\Models\Fee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class AdminController extends Controller
{
    // --- USERS CRUD ---
    public function index(Request $request)
    {
        $type = $request->query('type');
        $users = User::when($type, function($q) use ($type) {
            return $q->role($type);
        })->with('roles')->get();
        return response()->json($users);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|exists:roles,name'
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
        $user->assignRole($data['role']);

        return response()->json($user, 201);
    }

    public function show($id)
    {
        $user = User::with('roles')->findOrFail($id);
        return response()->json($user);
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $data = $request->validate([
            'name' => 'sometimes|string',
            'email' => 'sometimes|email|unique:users,email,' . $id,
            'role' => 'sometimes|exists:roles,name'
        ]);

        $user->update($data);
        if (isset($data['role'])) {
            $user->syncRoles($data['role']);
        }

        return response()->json($user);
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return response()->json(['message' => 'User deleted']);
    }

    // --- DEPARTMENTS CRUD ---
    public function departmentsIndex() { return response()->json(Department::all()); }
    public function departmentsStore(Request $r) { return response()->json(Department::create($r->all()), 201); }
    public function departmentsShow($id) { return response()->json(Department::findOrFail($id)); }
    public function departmentsUpdate(Request $r, $id) { $d = Department::findOrFail($id); $d->update($r->all()); return response()->json($d); }
    public function departmentsDestroy($id) { Department::findOrFail($id)->delete(); return response()->json(['message' => 'Deleted']); }

    // --- COURSES CRUD ---
    public function coursesIndex() { return response()->json(Course::with('department', 'instructor')->get()); }
    public function coursesStore(Request $r) { return response()->json(Course::create($r->all()), 201); }
    public function coursesShow($id) { return response()->json(Course::with('department', 'instructor')->findOrFail($id)); }
    public function coursesUpdate(Request $r, $id) { $c = Course::findOrFail($id); $c->update($r->all()); return response()->json($c); }
    public function coursesDestroy($id) { Course::findOrFail($id)->delete(); return response()->json(['message' => 'Deleted']); }

    // --- BOOKS CRUD ---
    public function booksIndex() { return response()->json(Book::all()); }
    public function booksStore(Request $r) { return response()->json(Book::create($r->all()), 201); }
    public function booksShow($id) { return response()->json(Book::findOrFail($id)); }
    public function booksUpdate(Request $r, $id) { $b = Book::findOrFail($id); $b->update($r->all()); return response()->json($b); }
    public function booksDestroy($id) { Book::findOrFail($id)->delete(); return response()->json(['message' => 'Deleted']); }

    // --- NOTICES CRUD ---
    public function noticesIndex() { return response()->json(Notice::orderBy('created_at', 'desc')->get()); }
    public function noticesStore(Request $r) {
        $data = $r->validate(['title'=>'required','content'=>'required','audience'=>'required']);
        $data['author_id'] = auth()->id();
        $data['published_at'] = now();
        return response()->json(Notice::create($data), 201);
    }
    public function noticesUpdate(Request $r, $id) { $n = Notice::findOrFail($id); $n->update($r->all()); return response()->json($n); }
    public function noticesDestroy($id) { Notice::findOrFail($id)->delete(); return response()->json(['message' => 'Deleted']); }

    // --- ANALYTICS ---
    public function analytics()
    {
        $enrollments = Enrollment::count();
        $revenue = Fee::where('status', 'paid')->sum('amount');
        $users = User::count();

        return response()->json([
            'enrollments' => $enrollments,
            'revenue' => $revenue,
            'users' => $users
        ]);
    }
}