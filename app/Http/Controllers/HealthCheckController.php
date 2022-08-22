<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class HealthCheckController extends Controller
{
    public function index()
    {
        try {
            User::with(
                'budgets',
                'roles',
                'permissions',
                'transactions',
                'budget_periods',
            )->get();
            return response('All good!', 200);
        } catch (Exception $e) {
            Log::error('Health check failed', ['error' => $e->getMessage()]);
            return response('Health check failed', 500);
        }
    }
}
