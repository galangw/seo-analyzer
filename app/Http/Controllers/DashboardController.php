<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user();
        $recentContents = $user->contents()
            ->with('latestSeoResult')
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard', compact('recentContents'));
    }
}
