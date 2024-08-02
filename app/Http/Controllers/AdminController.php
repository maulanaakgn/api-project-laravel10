<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', User::class);

        $users = User::all();

        return response()->json($users);
    }
}

