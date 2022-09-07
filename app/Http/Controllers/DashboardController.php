<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Budget;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        return Inertia::render('Dashboard', [
            'budgets' => Budget::whereUserId(Auth::id())->with('currency', 'intervals', 'time_period')->get()->toArray(),
        ]);
    }
}
