<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Transaction;

class DashboardController extends Controller
{
    public function getDashboardData(Request $request)
    {
        $sumCategories = Transaction::select('category', DB::raw('sum(value) as sum'))
            ->where('user_id', Auth::id())
            ->groupBy('category')
            ->orderBy('category', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'sumCategories' => $sumCategories,
                'generalSum' => $sumCategories->sum('sum'),
            ],
        ], 200);
    }
}
