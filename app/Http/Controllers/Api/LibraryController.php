<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LibraryController extends Controller
{
    public function index() { return response()->json(['books' => []]); }
    public function show($id) { return response()->json(['book' => []]); }
}