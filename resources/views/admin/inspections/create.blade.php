@extends('layouts.admin')

@section('title', 'Schedule Site Inspection')
@section('header', 'Book Site Inspection')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <div>
        <a href="{{ route('admin.inspections.index') }}" class="text-xs text-slate-400 hover:text-emerald-400 flex items-center gap-1 mb-2">
            &larr; Back to Inspections Agenda
        </a>
        <h2 class="text-2xl font-bold text-white tracking-tight">Schedule Site Inspection</h2>
        <p class="text-xs text-slate-400 mt-1">
            Book a physical site visit appointment for a prospective buyer.
        </p>
    </div>

    @if ($errors->any())
        <div class="p-4 rounded-2xl bg-rose-950/60 border border-rose-800 text-rose-300 text-xs space-y-1">
            <p class="font-bold">Please correct the following errors:</p>
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.inspections.store') }}" class="space-y-6">
        @csrf

        @if ($selectedEnquiryId)
            <input type="hidden" name="enquiry_id" value="{{ $selectedEnquiryId }}">
        @endif

        <div class="p-6 sm:p-8 rounded-2xl bg-slate-900 border border-slate-800 space-y-6 shadow-xl">
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 pb-3 border-b border-slate-800 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                Inspection Participants & Target Property
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Property Selector -->
                <div>
                    <label class="block text-2xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">
                        Property to Inspect <span class="text-rose-400">*</span>
                    </label>
                    <select name="property_id" required
                            class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-xs text-white focus:outline-hidden focus:border-emerald-500">
                        <option value="">Select Target Property</option>
                        @foreach ($properties as $prop)
                            <option value="{{ $prop->id }}" {{ old('property_id', $selectedPropertyId) == $prop->id ? 'selected' : '' }}>
                                [{{ $prop->reference }}] {{ $prop->title }} ({{ $prop->neighbourhood }}, {{ $prop->lga }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Customer Selector -->
                <div>
                    <label class="block text-2xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">
                        Client / Attendee <span class="text-rose-400">*</span>
                    </label>
                    <select name="customer_id" required
                            class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-xs text-white focus:outline-hidden focus:border-emerald-500">
                        <option value="">Select Client</option>
                        @foreach ($customers as $cust)
                            <option value="{{ $cust->id }}" {{ old('customer_id', $selectedCustomerId) == $cust->id ? 'selected' : '' }}>
                                {{ $cust->name }} ({{ $cust->phone }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 pt-4 pb-3 border-b border-slate-800 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                Date, Timing & Landmark Meeting Point
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <!-- Date -->
                <div>
                    <label class="block text-2xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">
                        Inspection Date <span class="text-rose-400">*</span>
                    </label>
                    <input type="date" name="scheduled_date" value="{{ old('scheduled_date', now()->addDay()->toDateString()) }}" required
                           class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-xs text-white focus:outline-hidden focus:border-emerald-500">
                </div>

                <!-- Time -->
                <div>
                    <label class="block text-2xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">
                        Meeting Time <span class="text-rose-400">*</span>
                    </label>
                    <input type="text" name="scheduled_time" value="{{ old('scheduled_time', '10:00 AM') }}" required
                           placeholder="e.g. 10:00 AM, 14:00"
                           class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-xs text-white placeholder-slate-500 focus:outline-hidden focus:border-emerald-500">
                </div>

                <!-- Status -->
                <div>
                    <label class="block text-2xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">
                        Status <span class="text-rose-400">*</span>
                    </label>
                    <select name="status" required
                            class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-xs text-white focus:outline-hidden focus:border-emerald-500">
                        @foreach (\App\Models\Inspection::STATUSES as $code => $label)
                            <option value="{{ $code }}" {{ old('status', 'confirmed') === $code ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Meeting Location / Landmark -->
            <div>
                <label class="block text-2xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">
                    Landmark Meeting Point / Rendezvous Point <span class="text-rose-400">*</span>
                </label>
                <input type="text" name="meeting_location" value="{{ old('meeting_location') }}" required
                       placeholder="e.g. Eleko Beach Junction Total Filling Station, or Directly at Site Gate"
                       class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-xs text-white placeholder-slate-500 focus:outline-hidden focus:border-emerald-500">
                <p class="text-3xs text-slate-500 mt-1">
                    Clear physical landmarks make it easy for clients navigating Nigerian roads to meet Victor.
                </p>
            </div>

            <!-- Notes -->
            <div>
                <label class="block text-2xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">
                    Special Preparation Notes & Logistical Details <span class="text-slate-500 font-normal">(Optional)</span>
                </label>
                <textarea name="notes" rows="2"
                          placeholder="e.g. Bring survey plan copy and red tape; client arriving in convoy; inspect corner beacons..."
                          class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-xs text-white placeholder-slate-500 focus:outline-hidden focus:border-emerald-500">{{ old('notes') }}</textarea>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('admin.inspections.index') }}"
               class="px-5 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-slate-300 font-semibold text-xs border border-slate-800 transition-colors">
                Cancel
            </a>
            <button type="submit"
                    class="px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-lg shadow-emerald-950 transition-all cursor-pointer">
                Confirm & Add to Schedule
            </button>
        </div>
    </form>

</div>
@endsection

