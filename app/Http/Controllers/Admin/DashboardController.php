<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Enquiry;
use App\Models\Inspection;
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
            'new_enquiries' => Enquiry::where('status', Enquiry::STATUS_NEW)->count(),
            'upcoming_inspections' => Inspection::upcoming()->count(),
            'total_customers' => Customer::count(),
        ];

        $recentProperties = Property::with(['category', 'coverImage'])
            ->latest()
            ->take(5)
            ->get();

        $recentEnquiries = Enquiry::with(['property', 'customer'])
            ->latest()
            ->take(5)
            ->get();

        $upcomingInspections = Inspection::with(['property', 'customer'])
            ->upcoming()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'stats',
            'recentProperties',
            'recentEnquiries',
            'upcomingInspections'
        ));
    }
}
