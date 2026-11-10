<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreInspectionRequest;
use App\Http\Requests\Admin\UpdateInspectionRequest;
use App\Models\Customer;
use App\Models\Enquiry;
use App\Models\Inspection;
use App\Models\Property;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InspectionController extends Controller
{
    /**
     * Display a listing of inspections (upcoming & past).
     */
    public function index(Request $request): View
    {
        $tab = $request->input('tab', 'upcoming');

        $query = Inspection::with(['property', 'customer', 'enquiry']);

        if ($tab === 'past') {
            $query->past();
        } else {
            $query->upcoming();
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('property_id')) {
            $query->where('property_id', $request->input('property_id'));
        }

        $inspections = $query->paginate(15)->withQueryString();

        $upcomingCount = Inspection::upcoming()->count();
        $pastCount = Inspection::past()->count();
        $properties = Property::select('id', 'title', 'reference')->orderBy('title')->get();

        return view('admin.inspections.index', compact(
            'inspections',
            'tab',
            'upcomingCount',
            'pastCount',
            'properties'
        ));
    }

    /**
     * Show form to schedule a new site inspection.
     */
    public function create(Request $request): View
    {
        $selectedPropertyId = $request->input('property_id');
        $selectedCustomerId = $request->input('customer_id');
        $selectedEnquiryId = $request->input('enquiry_id');

        // If enquiry_id is passed, automatically load property_id and customer_id from it
        if ($selectedEnquiryId && (!$selectedPropertyId || !$selectedCustomerId)) {
            $enquiry = Enquiry::find($selectedEnquiryId);
            if ($enquiry) {
                $selectedPropertyId = $selectedPropertyId ?: $enquiry->property_id;
                $selectedCustomerId = $selectedCustomerId ?: $enquiry->customer_id;
            }
        }

        $properties = Property::select('id', 'title', 'reference', 'neighbourhood', 'lga', 'state')
            ->orderBy('title')
            ->get();

        $customers = Customer::select('id', 'name', 'phone')
            ->orderBy('name')
            ->get();

        return view('admin.inspections.create', compact(
            'properties',
            'customers',
            'selectedPropertyId',
            'selectedCustomerId',
            'selectedEnquiryId'
        ));
    }

    /**
     * Store a newly scheduled site inspection.
     */
    public function store(StoreInspectionRequest $request): RedirectResponse
    {
        $inspection = Inspection::create($request->validated());

        // If linked to an enquiry, update the enquiry status to inspection_scheduled
        if ($inspection->enquiry_id) {
            $enquiry = Enquiry::find($inspection->enquiry_id);
            if ($enquiry && in_array($enquiry->status, [Enquiry::STATUS_NEW, Enquiry::STATUS_CONTACTED])) {
                $enquiry->update(['status' => Enquiry::STATUS_INSPECTION_SCHEDULED]);
            }
        }

        return redirect()->route('admin.inspections.show', $inspection)
            ->with('status', 'Site inspection scheduled successfully.');
    }

    /**
     * Display the specified site inspection details.
     */
    public function show(Inspection $inspection): View
    {
        $inspection->load(['property.images', 'customer', 'enquiry']);

        return view('admin.inspections.show', compact('inspection'));
    }

    /**
     * Show form to reschedule or edit an inspection.
     */
    public function edit(Inspection $inspection): View
    {
        $properties = Property::select('id', 'title', 'reference')->orderBy('title')->get();
        $customers = Customer::select('id', 'name', 'phone')->orderBy('name')->get();

        return view('admin.inspections.edit', compact('inspection', 'properties', 'customers'));
    }

    /**
     * Update inspection appointment details or status.
     */
    public function update(UpdateInspectionRequest $request, Inspection $inspection): RedirectResponse
    {
        $inspection->update($request->validated());

        return redirect()->route('admin.inspections.show', $inspection)
            ->with('status', 'Inspection appointment updated successfully.');
    }

    /**
     * Delete or cancel an inspection.
     */
    public function destroy(Inspection $inspection): RedirectResponse
    {
        $inspection->delete();

        return redirect()->route('admin.inspections.index')
            ->with('status', 'Inspection appointment removed.');
    }
}

