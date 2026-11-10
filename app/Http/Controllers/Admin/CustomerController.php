<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCustomerRequest;
use App\Http\Requests\Admin\UpdateCustomerRequest;
use App\Models\Customer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerController extends Controller
{
    /**
     * Display a listing of prospective buyers and clients.
     */
    public function index(Request $request): View
    {
        $customers = Customer::withCount(['enquiries', 'inspections'])
            ->search($request->input('search'))
            ->when($request->filled('source'), function ($q) use ($request) {
                $q->where('source', $request->input('source'));
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $totalCustomers = Customer::count();

        return view('admin.customers.index', compact('customers', 'totalCustomers'));
    }

    /**
     * Show form for creating a new customer profile.
     */
    public function create(): View
    {
        return view('admin.customers.create');
    }

    /**
     * Store a newly created customer profile.
     */
    public function store(StoreCustomerRequest $request): RedirectResponse
    {
        $customer = Customer::create($request->validated());

        return redirect()->route('admin.customers.show', $customer)
            ->with('status', 'Customer profile created successfully.');
    }

    /**
     * Display a 360-degree view of the customer.
     */
    public function show(Customer $customer): View
    {
        $customer->load([
            'enquiries.property',
            'inspections.property',
        ]);

        return view('admin.customers.show', compact('customer'));
    }

    /**
     * Show the form for editing the customer profile.
     */
    public function edit(Customer $customer): View
    {
        return view('admin.customers.edit', compact('customer'));
    }

    /**
     * Update the customer profile.
     */
    public function update(UpdateCustomerRequest $request, Customer $customer): RedirectResponse
    {
        $customer->update($request->validated());

        return redirect()->route('admin.customers.show', $customer)
            ->with('status', 'Customer details updated successfully.');
    }

    /**
     * Soft delete a customer profile.
     */
    public function destroy(Customer $customer): RedirectResponse
    {
        $customer->delete();

        return redirect()->route('admin.customers.index')
            ->with('status', 'Customer profile archived.');
    }
}

