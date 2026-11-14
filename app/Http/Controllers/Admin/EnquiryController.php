<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreManualLeadRequest;
use App\Models\Customer;
use App\Models\Enquiry;
use App\Models\Property;
use App\Services\ActivityLogger;
use App\Services\CustomerManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EnquiryController extends Controller
{
    public function __construct(
        protected CustomerManager $customerManager
    ) {}

    /**
     * Display a listing of enquiries.
     */
    public function index(Request $request): View
    {
        $statusCounts = [
            'all' => Enquiry::count(),
            'new' => Enquiry::where('status', Enquiry::STATUS_NEW)->count(),
            'contacted' => Enquiry::where('status', Enquiry::STATUS_CONTACTED)->count(),
            'inspection_scheduled' => Enquiry::where('status', Enquiry::STATUS_INSPECTION_SCHEDULED)->count(),
            'negotiation' => Enquiry::where('status', Enquiry::STATUS_NEGOTIATION)->count(),
            'converted' => Enquiry::where('status', Enquiry::STATUS_CONVERTED)->count(),
            'closed_lost' => Enquiry::where('status', Enquiry::STATUS_CLOSED_LOST)->count(),
        ];

        $enquiries = Enquiry::with(['property', 'customer', 'inspection'])
            ->filter($request->only(['status', 'source', 'property_id', 'search']))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $properties = Property::select('id', 'title', 'reference')->orderBy('title')->get();

        return view('admin.enquiries.index', compact('enquiries', 'statusCounts', 'properties'));
    }

    /**
     * Show the form for recording a manual offline lead (phone/WhatsApp).
     */
    public function create(): View
    {
        $properties = Property::select('id', 'title', 'reference', 'price')
            ->orderBy('title')
            ->get();

        return view('admin.enquiries.create', compact('properties'));
    }

    /**
     * Store a manually recorded lead.
     */
    public function store(StoreManualLeadRequest $request): RedirectResponse
    {
        $customer = $this->customerManager->findOrCreateCustomer([
            'name' => $request->validated('name'),
            'phone' => $request->validated('phone'),
            'email' => $request->validated('email'),
            'source' => $request->validated('source'),
            'notes' => $request->validated('internal_notes'),
        ]);

        $enquiry = Enquiry::create([
            'customer_id' => $customer->id,
            'property_id' => $request->validated('property_id'),
            'name' => $request->validated('name'),
            'phone' => $request->validated('phone'),
            'email' => $request->validated('email'),
            'message' => $request->validated('message'),
            'source' => $request->validated('source'),
            'status' => $request->validated('status'),
            'internal_notes' => $request->validated('internal_notes'),
        ]);

        ActivityLogger::log(
            'lead.created',
            "Recorded manual lead from {$enquiry->name} ({$enquiry->phone})",
            $enquiry
        );

        return redirect()->route('admin.enquiries.show', $enquiry)
            ->with('status', 'Lead recorded and customer profile updated successfully.');
    }

    /**
     * Display the specified enquiry.
     */
    public function show(Enquiry $enquiry): View
    {
        $enquiry->load(['customer.enquiries.property', 'customer.inspections.property', 'property', 'inspection']);

        return view('admin.enquiries.show', compact('enquiry'));
    }

    /**
     * Update the enquiry status or internal notes.
     */
    public function update(Request $request, Enquiry $enquiry): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'string', 'in:' . implode(',', array_keys(Enquiry::STATUSES))],
            'internal_notes' => ['nullable', 'string', 'max:5000'],
        ]);

        $oldStatus = $enquiry->status;
        $enquiry->update($validated);

        ActivityLogger::log(
            'lead.status_updated',
            "Updated lead #{$enquiry->id} status to " . (Enquiry::STATUSES[$validated['status']] ?? $validated['status']),
            $enquiry,
            ['old_status' => $oldStatus, 'new_status' => $validated['status']]
        );

        return back()->with('status', 'Enquiry status and notes updated.');
    }

    /**
     * Remove the specified enquiry.
     */
    public function destroy(Enquiry $enquiry): RedirectResponse
    {
        $name = $enquiry->name;
        $id = $enquiry->id;

        ActivityLogger::log(
            'lead.deleted',
            "Deleted enquiry #{$id} from {$name}"
        );

        $enquiry->delete();

        return redirect()->route('admin.enquiries.index')
            ->with('status', 'Enquiry record deleted.');
    }
}

