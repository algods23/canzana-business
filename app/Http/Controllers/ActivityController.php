<?php

namespace App\Http\Controllers;

use App\Data\MockData;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    public function index(Request $request)
    {
        $activities = MockData::activities();

        if ($type = $request->get('type')) {
            $activities = array_filter($activities, fn ($a) => $a['type'] === $type);
        }

        return view('activity.index', [
            'activities' => array_values($activities),
            'filters' => $request->only(['type']),
        ]);
    }
}
