<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function dashboard(){

        $user = Auth::user();
        return Inertia::render('dashboard/Main', [
            'user' => $user,
        ]);
    }
}
