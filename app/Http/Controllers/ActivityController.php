<?php

namespace App\Http\Controllers;

use App\Support\Analytics;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    public function index(Request $request)
    {
        $activities = collect(Analytics::recentActivities(20));

        if ($type = $request->get('type')) {
            $activities = $activities->where('type', $type);
        }

        return view('activity.index', [
            'activities' => $activities->values(),
            'filters' => $request->only(['type']),
        ]);
    }
}
