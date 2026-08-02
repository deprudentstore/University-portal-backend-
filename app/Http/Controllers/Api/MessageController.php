<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function index() { return response()->json(['messages' => []]); }
    public function store(Request $r) { return response()->json(['message' => 'Sent']); }
    public function show($id) { return response()->json(['message' => []]); }
}