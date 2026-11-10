@extends('layouts.admin')

@section('title', 'Reschedule / Update Inspection')
@section('header', 'Update Site Inspection')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <div>
        <a href="{{ route('admin.inspections.show', $inspection) }}" class="text-xs text-slate-400 hover:text-emerald-400 flex items-center gap-1 mb-2">
            &larr; Back to Inspection Details
        </a>
        <h2 class="text-2xl font-bold text-white tracking-tight">Update Site Inspection</h2>
        <p class="text-xs text-slate-400 mt-1">
            Reschedule date/time, change meeting point, or record post-inspection outcome.
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

    <form method="POST" action="{{ route('admin.inspections.update', $inspection) }}" class="space-y-6">
        @csrf
        @method('PUT')

        @if ($inspection->enquiry_id)
            <input type="hidden" name="enquiry_id" value="{{ $inspection->enquiry_id }}">
        @endif

        <div class="p-6 sm:p-8 rounded-2xl bg-slate-900 border border-slate-800 space-y-6 shadow-xl">
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 pb-3 border-b border-slate-800 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                Inspection Participants & Target Property
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-2xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">
                        Property <span class="text-rose-400">*</span>
                    </label>
                    <select name="property_id" required
                            class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-xs text-white focus:outline-hidden focus:border-emerald-500">
                        @foreach ($properties as $prop)
                            <option value="{{ $prop->id }}" {{ old('property_id', $inspection->property_id) == $prop->id ? 'selected' : '' }}>
                                [{{ $prop->reference }}] {{ $prop->title }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-2xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">
                        Client / Attendee <span class="text-rose-400">*</span>
                    </label>
                    <select name="customer_id" required
                            class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-xs text-white focus:outline-hidden focus:border-emerald-500">
                        @foreach ($customers as $cust)
                            <option value="{{ $cust->id }}" {{ old('customer_id', $inspection->customer_id) == $cust->id ? 'selected' : '' }}>
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
                <div>
                    <label class="block text-2xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">
                        Inspection Date <span class="text-rose-400">*</span>
                    </label>
                    <input type="date" name="scheduled_date" value="{{ old('scheduled_date', $inspection->scheduled_date ? $inspection->scheduled_date->toDateString() : '') }}" required
                           class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-xs text-white focus:outline-hidden focus:border-emerald-500">
                </div>

                <div>
                    <label class="block text-2xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">
                        Meeting Time <span class="text-rose-400">*</span>
                    </label>
                    <input type="text" name="scheduled_time" value="{{ old('scheduled_time', $inspection->scheduled_time) }}" required
                           class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-xs text-white placeholder-slate-500 focus:outline-hidden focus:border-emerald-500">
                </div>

                <div>
                    <label class="block text-2xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">
                        Status <span class="text-rose-400">*</span>
                    </label>
                    <select name="status" required
                            class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-xs text-white focus:outline-hidden focus:border-emerald-500">
                        @foreach (\App\Models\Inspection::STATUSES as $code => $label)
                            <option value="{{ $code }}" {{ old('status', $inspection->status) === $code ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-2xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">
                    Landmark Meeting Point / Rendezvous Point <span class="text-rose-400">*</span>
                </label>
                <input type="text" name="meeting_location" value="{{ old('meeting_location', $inspection->meeting_location) }}" required
                       class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-xs text-white placeholder-slate-500 focus:outline-hidden focus:border-emerald-500">
            </div>

            <div>
                <label class="block text-2xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">
                    Post-Inspection Outcome / Preparation Notes
                </label>
                <textarea name="notes" rows="3"
                          placeholder="Record client impressions, price counter-offers, follow-up dates..."
                          class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-xs text-white placeholder-slate-500 focus:outline-hidden focus:border-emerald-500">{{ old('notes', $inspection->notes) }}</textarea>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('admin.inspections.show', $inspection) }}"
               class="px-5 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-slate-300 font-semibold text-xs border border-slate-800 transition-colors">
                Cancel
            </a>
            <button type="submit"
                    class="px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-lg shadow-emerald-950 transition-all cursor-pointer">
                Save Changes
            </button>
        </div>
    </form>

</div>
@endsection

