<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the admin overview dashboard.
     */
    public function index(Request $request): View
    {
        // Real counts for Phase 1 (features are in later phases, no fake stats)
        $stats = [
            'total_properties' => 0,
            'published_properties' => 0,
            'draft_properties' => 0,
            'new_enquiries' => 0,
            'upcoming_inspections' => 0,
        ];

        return view('admin.dashboard', compact('stats'));
    }
}
