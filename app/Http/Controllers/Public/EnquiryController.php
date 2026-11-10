<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\Public\StoreEnquiryRequest;
use App\Models\Customer;
use App\Models\Enquiry;
use App\Models\Property;
use App\Services\CustomerManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\RateLimiter;

class EnquiryController extends Controller
{
    public function __construct(
        protected CustomerManager $customerManager
    ) {}

    /**
     * Store an enquiry submitted from the public website.
     */
    public function store(StoreEnquiryRequest $request): RedirectResponse
    {
        // 1. Silent honeypot bot trap
        if ($request->filled('bot_check')) {
            // Silently pretend success to deceive spam bots
            return back()->with('enquiry_success', 'Thank you! Your enquiry has been received.');
        }

        // 2. IP Rate limiting (max 5 per minute per IP)
        $throttleKey = 'enquiry:' . $request->ip();
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return back()->withErrors([
                'enquiry_throttle' => "Too many enquiry submissions. Please wait {$seconds} seconds before trying again."
            ])->withInput();
        }

        RateLimiter::hit($throttleKey, 60);

        // 3. De-duplicate or create Customer profile
        $customer = $this->customerManager->findOrCreateCustomer([
            'name' => $request->validated('name'),
            'phone' => $request->validated('phone'),
            'email' => $request->validated('email'),
            'source' => Customer::SOURCE_WEBSITE,
        ]);

        // 4. Create the Enquiry record
        $enquiry = Enquiry::create([
            'customer_id' => $customer->id,
            'property_id' => $request->validated('property_id'),
            'name' => $request->validated('name'),
            'phone' => $request->validated('phone'),
            'email' => $request->validated('email'),
            'message' => $request->validated('message'),
            'source' => Enquiry::SOURCE_WEBSITE,
            'status' => Enquiry::STATUS_NEW,
        ]);

        return back()->with('enquiry_success', 'Thank you! Your enquiry has been received. Victor Akande will review your request and get in touch shortly.');
    }
}

