@extends('layouts.admin')

@section('title', 'Client Profile 360°: ' . $customer->name)
@section('header', 'Client CRM 360° Profile')

@section('content')
<div class="space-y-6">

    <!-- Back & Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <a href="{{ route('admin.customers.index') }}" class="text-xs text-slate-400 hover:text-emerald-400 flex items-center gap-1 mb-2">
                &larr; Back to Customers Directory
            </a>
            <div class="flex items-center gap-3">
                <h2 class="text-2xl font-bold text-white tracking-tight">{{ $customer->name }}</h2>
                <span class="px-2.5 py-0.5 rounded-full text-2xs font-extrabold uppercase tracking-wider bg-slate-800 text-slate-300 border border-slate-700">
                    Source: {{ \App\Models\Customer::SOURCES[$customer->source] ?? ucfirst($customer->source) }}
                </span>
            </div>
            <p class="text-xs text-slate-400 mt-1">
                Client profile since {{ $customer->created_at->format('M j, Y') }} ({{ $customer->created_at->diffForHumans() }})
            </p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('admin.inspections.create', ['customer_id' => $customer->id]) }}"
               class="px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-lg shadow-emerald-950 transition-all flex items-center gap-2 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                Book Site Inspection
            </a>

            <a href="{{ route('admin.customers.edit', $customer) }}"
               class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-white font-semibold text-xs border border-slate-700 transition-colors">
                Edit Details
            </a>

            <form method="POST" action="{{ route('admin.customers.destroy', $customer) }}"
                  onsubmit="return confirm('Archive this customer profile?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="p-2.5 rounded-xl bg-slate-900 hover:bg-rose-950 text-slate-400 hover:text-rose-400 border border-slate-800 transition-colors cursor-pointer" title="Archive customer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </button>
            </form>
        </div>
    </div>

    @if (session('status'))
        <div class="p-4 rounded-2xl bg-emerald-950/80 border border-emerald-800 text-emerald-300 text-xs flex items-center gap-2">
            <svg class="w-4 h-4 shrink-0 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            <span>{{ session('status') }}</span>
        </div>
    @endif

    <!-- 360° Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
        <!-- Phone & Direct WhatsApp -->
        <div class="p-5 rounded-2xl bg-slate-900 border border-slate-800 space-y-2">
            <span class="text-2xs font-bold uppercase tracking-wider text-slate-400">Phone Number</span>
            <div class="text-base font-bold text-white font-mono">{{ $customer->phone }}</div>
            @php
                $cleanPhone = preg_replace('/[^0-9]/', '', $customer->phone);
                if (str_starts_with($cleanPhone, '0') && strlen($cleanPhone) === 11) {
                    $waPhone = '234' . substr($cleanPhone, 1);
                } else {
                    $waPhone = $cleanPhone;
                }
            @endphp
            <div class="flex items-center gap-2 pt-1">
                <a href="https://wa.me/{{ $waPhone }}" target="_blank" class="text-2xs font-bold text-emerald-400 hover:underline flex items-center gap-1">
                    WhatsApp &rarr;
                </a>
                <span class="text-slate-600">•</span>
                <a href="tel:{{ $customer->phone }}" class="text-2xs font-bold text-sky-400 hover:underline">
                    Direct Call &rarr;
                </a>
            </div>
        </div>

        <!-- Email -->
        <div class="p-5 rounded-2xl bg-slate-900 border border-slate-800 space-y-2">
            <span class="text-2xs font-bold uppercase tracking-wider text-slate-400">Email Address</span>
            <div class="text-sm font-bold text-white truncate">
                {{ $customer->email ?: 'Not provided' }}
            </div>
            @if ($customer->email)
                <a href="mailto:{{ $customer->email }}" class="text-2xs font-bold text-emerald-400 hover:underline block pt-1">
                    Send Email &rarr;
                </a>
            @endif
        </div>

        <!-- Total Inquiries -->
        <div class="p-5 rounded-2xl bg-slate-900 border border-slate-800 space-y-2">
            <span class="text-2xs font-bold uppercase tracking-wider text-slate-400">Total Enquiries</span>
            <div class="text-2xl font-black text-sky-400 font-mono">{{ $customer->enquiries->count() }}</div>
            <p class="text-3xs text-slate-500">Inbound lead records</p>
        </div>

        <!-- Total Inspections -->
        <div class="p-5 rounded-2xl bg-slate-900 border border-slate-800 space-y-2">
            <span class="text-2xs font-bold uppercase tracking-wider text-slate-400">Scheduled Inspections</span>
            <div class="text-2xl font-black text-amber-400 font-mono">{{ $customer->inspections->count() }}</div>
            <p class="text-3xs text-slate-500">Site visits arranged</p>
        </div>
    </div>

    <!-- Confidential Client Notes -->
    @if ($customer->notes)
        <div class="p-5 rounded-2xl bg-slate-900 border border-slate-800 space-y-2 shadow-sm">
            <h3 class="text-xs font-bold uppercase tracking-wider text-amber-400 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                Victor's Confidential Notes & Client Preferences
            </h3>
            <p class="text-xs text-slate-200 leading-relaxed whitespace-pre-wrap">{{ $customer->notes }}</p>
        </div>
    @endif

    <!-- Activity Section: Enquiries & Inspections -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- Left: Enquiry History -->
        <div class="p-6 rounded-2xl bg-slate-900 border border-slate-800 space-y-4 shadow-xl">
            <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                <h3 class="text-xs font-bold uppercase tracking-wider text-white flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-sky-400"></span>
                    Enquiry History ({{ $customer->enquiries->count() }})
                </h3>
            </div>

            <div class="space-y-3">
                @forelse ($customer->enquiries as $enquiry)
                    <div class="p-4 rounded-xl bg-slate-950 border border-slate-800 space-y-2">
                        <div class="flex items-center justify-between text-2xs">
                            <span class="text-slate-400">{{ $enquiry->created_at->format('M j, Y • h:i A') }}</span>
                            <span class="px-2 py-0.5 rounded text-3xs font-extrabold uppercase bg-slate-800 text-slate-300">
                                {{ \App\Models\Enquiry::STATUSES[$enquiry->status] ?? $enquiry->status }}
                            </span>
                        </div>

                        @if ($enquiry->property)
                            <a href="{{ route('admin.properties.show', $enquiry->property) }}" class="text-xs font-bold text-white hover:text-emerald-400 block truncate">
                                [{{ $enquiry->property->reference }}] {{ $enquiry->property->title }}
                            </a>
                        @endif

                        <p class="text-xs text-slate-300 line-clamp-2">{{ $enquiry->message }}</p>

                        <div class="pt-2 border-t border-slate-900 flex justify-end">
                            <a href="{{ route('admin.enquiries.show', $enquiry) }}" class="text-2xs text-emerald-400 hover:underline">
                                View Full Enquiry &rarr;
                            </a>
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-slate-500 py-4 text-center">No inquiries logged yet.</p>
                @endforelse
            </div>
        </div>

        <!-- Right: Inspection History -->
        <div class="p-6 rounded-2xl bg-slate-900 border border-slate-800 space-y-4 shadow-xl">
            <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                <h3 class="text-xs font-bold uppercase tracking-wider text-white flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-amber-400"></span>
                    Site Inspections ({{ $customer->inspections->count() }})
                </h3>
            </div>

            <div class="space-y-3">
                @forelse ($customer->inspections as $inspection)
                    <div class="p-4 rounded-xl bg-slate-950 border border-slate-800 space-y-2">
                        <div class="flex items-center justify-between text-2xs">
                            <span class="font-bold text-white">{{ $inspection->formatted_date }} at {{ $inspection->scheduled_time }}</span>
                            <span class="px-2 py-0.5 rounded text-3xs font-extrabold uppercase bg-amber-950 text-amber-300 border border-amber-800">
                                {{ \App\Models\Inspection::STATUSES[$inspection->status] ?? $inspection->status }}
                            </span>
                        </div>

                        <a href="{{ route('admin.properties.show', $inspection->property) }}" class="text-xs font-bold text-emerald-400 hover:underline block truncate">
                            [{{ $inspection->property->reference }}] {{ $inspection->property->title }}
                        </a>

                        <p class="text-2xs text-slate-400">
                            Meeting: <strong class="text-slate-200">{{ $inspection->meeting_location }}</strong>
                        </p>

                        <div class="pt-2 border-t border-slate-900 flex justify-end">
                            <a href="{{ route('admin.inspections.show', $inspection) }}" class="text-2xs text-emerald-400 hover:underline">
                                Inspection Agenda &rarr;
                            </a>
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-slate-500 py-4 text-center">No site inspections scheduled yet.</p>
                @endforelse
            </div>
        </div>

    </div>

</div>
@endsection

