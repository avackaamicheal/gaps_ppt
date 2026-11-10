<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the admin overview dashboard with live operational statistics.
     */
    public function index(Request $request): View
    {
        $stats = [
            'total_properties' => Property::count(),
            'published_properties' => Property::where('status', Property::STATUS_PUBLISHED)->count(),
            'draft_properties' => Property::where('status', Property::STATUS_DRAFT)->count(),
            'under_negotiation' => Property::where('status', Property::STATUS_UNDER_NEGOTIATION)->count(),
            'sold_properties' => Property::where('status', Property::STATUS_SOLD)->count(),
            'new_enquiries' => 0, // Phase 3
            'upcoming_inspections' => 0, // Phase 3
        ];

        $recentProperties = Property::with(['category', 'coverImage'])
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentProperties'));
    }
}
