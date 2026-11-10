@extends('layouts.admin')

@section('title', 'Enquiry Details: ' . $enquiry->name)
@section('header', 'Enquiry & Lead Details')

@section('content')
<div class="space-y-6">

    <!-- Top Navigation & Action Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <a href="{{ route('admin.enquiries.index') }}" class="text-xs text-slate-400 hover:text-emerald-400 flex items-center gap-1 mb-2">
                &larr; Back to Enquiries
            </a>
            <div class="flex items-center gap-3">
                <h2 class="text-2xl font-bold text-white tracking-tight">Lead from {{ $enquiry->name }}</h2>
                <span class="px-2.5 py-0.5 rounded-full text-2xs font-extrabold uppercase tracking-wider bg-slate-800 text-slate-300 border border-slate-700">
                    Source: {{ \App\Models\Enquiry::SOURCES[$enquiry->source] ?? ucfirst($enquiry->source) }}
                </span>
            </div>
            <p class="text-xs text-slate-400 mt-1">
                Received on {{ $enquiry->created_at->format('l, F j, Y \a\t h:i A') }} ({{ $enquiry->created_at->diffForHumans() }})
            </p>
        </div>

        <div class="flex items-center gap-2">
            <!-- Schedule Inspection CTA -->
            <a href="{{ route('admin.inspections.create', ['enquiry_id' => $enquiry->id, 'property_id' => $enquiry->property_id, 'customer_id' => $enquiry->customer_id]) }}"
               class="px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-lg shadow-emerald-950 transition-all flex items-center gap-2 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                Schedule Site Inspection
            </a>

            <!-- Delete Form -->
            <form method="POST" action="{{ route('admin.enquiries.destroy', $enquiry) }}"
                  onsubmit="return confirm('Are you sure you want to delete this enquiry record?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="p-2.5 rounded-xl bg-slate-900 hover:bg-rose-950 text-slate-400 hover:text-rose-400 border border-slate-800 transition-colors cursor-pointer" title="Delete enquiry">
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

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Left 2 Cols: Message, Status Updater, and Linked Property -->
        <div class="lg:col-span-2 space-y-6">

            <!-- The Enquiry Content -->
            <div class="p-6 sm:p-7 rounded-2xl bg-slate-900 border border-slate-800 space-y-4 shadow-xl">
                <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                        Client Message / Inquiry Details
                    </h3>
                    <span class="text-2xs font-mono text-slate-500">ID #{{ $enquiry->id }}</span>
                </div>

                <div class="p-4 rounded-xl bg-slate-950 border border-slate-800 text-slate-200 text-sm leading-relaxed whitespace-pre-wrap">
                    {{ $enquiry->message }}
                </div>

                <!-- Fast WhatsApp / Call Action Bar -->
                @php
                    $cleanPhone = preg_replace('/[^0-9]/', '', $enquiry->phone);
                    if (str_starts_with($cleanPhone, '0') && strlen($cleanPhone) === 11) {
                        $waPhone = '234' . substr($cleanPhone, 1);
                    } else {
                        $waPhone = $cleanPhone;
                    }
                    $replyMsg = "Hello {$enquiry->name}, this is Victor Akande regarding your inquiry about " . ($enquiry->property ? "property [{$enquiry->property->reference}] - {$enquiry->property->title}" : "our real estate listings") . ". How may I assist you?";
                @endphp
                <div class="flex flex-wrap items-center gap-3 pt-2">
                    <a href="https://wa.me/{{ $waPhone }}?text={{ urlencode($replyMsg) }}" target="_blank"
                       class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs flex items-center gap-2 transition-colors">
                        <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
                            <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
                        </svg>
                        Reply on WhatsApp
                    </a>

                    <a href="tel:{{ $enquiry->phone }}"
                       class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs flex items-center gap-2 transition-colors">
                        <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                        </svg>
                        Call {{ $enquiry->phone }}
                    </a>
                </div>
            </div>

            <!-- Status & Internal Notes Updater -->
            <div class="p-6 sm:p-7 rounded-2xl bg-slate-900 border border-slate-800 space-y-4 shadow-xl">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 pb-3 border-b border-slate-800 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                    Manage Pipeline Status & Confidential Notes
                </h3>

                <form method="POST" action="{{ route('admin.enquiries.update', $enquiry) }}" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-2xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">
                            Update Pipeline Status
                        </label>
                        <select name="status" class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-xs text-white focus:outline-hidden focus:border-emerald-500">
                            @foreach (\App\Models\Enquiry::STATUSES as $code => $label)
                                <option value="{{ $code }}" {{ $enquiry->status === $code ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-2xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">
                            Victor's Private Notes / Activity Log
                        </label>
                        <textarea name="internal_notes" rows="3"
                                  placeholder="Log conversation outcome, customer requirements, inspection feedback, price offers..."
                                  class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-xs text-white placeholder-slate-500 focus:outline-hidden focus:border-emerald-500">{{ old('internal_notes', $enquiry->internal_notes) }}</textarea>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit"
                                class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-md transition-all cursor-pointer">
                            Update Lead Status & Notes
                        </button>
                    </div>
                </form>
            </div>

            <!-- Referenced Property Card -->
            @if ($enquiry->property)
                <div class="p-6 rounded-2xl bg-slate-900 border border-slate-800 space-y-3 shadow-xl">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 pb-2 border-b border-slate-800 flex items-center justify-between">
                        <span>Property of Interest</span>
                        <a href="{{ route('admin.properties.show', $enquiry->property) }}" class="text-emerald-400 hover:underline text-2xs">
                            View Property &rarr;
                        </a>
                    </h3>

                    <div class="flex items-center gap-4">
                        @if ($enquiry->property->cover_url)
                            <img src="{{ $enquiry->property->cover_url }}" class="w-20 h-16 rounded-xl object-cover bg-slate-950">
                        @else
                            <div class="w-20 h-16 rounded-xl bg-slate-950 flex items-center justify-center text-slate-700 text-2xs">No Photo</div>
                        @endif

                        <div>
                            <span class="font-mono text-2xs font-bold text-emerald-400">[{{ $enquiry->property->reference }}]</span>
                            <h4 class="text-sm font-bold text-white">{{ $enquiry->property->title }}</h4>
                            <p class="text-xs text-slate-400 font-mono mt-0.5">{{ $enquiry->property->formatted_price }} • {{ $enquiry->property->display_location }}</p>
                        </div>
                    </div>
                </div>
            @endif

        </div>

        <!-- Right 1 Col: Customer Profile & Linked Activity -->
        <div class="space-y-6">

            <!-- Customer Summary Card -->
            <div class="p-6 rounded-2xl bg-slate-900 border border-slate-800 space-y-4 shadow-xl">
                <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">
                        Client Profile
                    </h3>
                    @if ($enquiry->customer)
                        <a href="{{ route('admin.customers.show', $enquiry->customer) }}" class="text-emerald-400 hover:underline text-2xs font-semibold">
                            Full CRM 360° &rarr;
                        </a>
                    @endif
                </div>

                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-tr from-emerald-500 to-teal-400 flex items-center justify-center text-slate-950 font-black text-base shadow">
                        {{ strtoupper(substr($enquiry->name, 0, 2)) }}
                    </div>
                    <div>
                        <h4 class="font-bold text-white text-sm">{{ $enquiry->name }}</h4>
                        <span class="text-2xs text-slate-400">Contact Person</span>
                    </div>
                </div>

                <div class="space-y-2 text-xs pt-2">
                    <div class="flex items-center justify-between text-slate-400">
                        <span>Phone:</span>
                        <a href="tel:{{ $enquiry->phone }}" class="text-white font-mono hover:text-emerald-400">{{ $enquiry->phone }}</a>
                    </div>
                    @if ($enquiry->email)
                        <div class="flex items-center justify-between text-slate-400">
                            <span>Email:</span>
                            <a href="mailto:{{ $enquiry->email }}" class="text-white hover:text-emerald-400 truncate max-w-[180px]">{{ $enquiry->email }}</a>
                        </div>
                    @endif
                    <div class="flex items-center justify-between text-slate-400">
                        <span>Customer ID:</span>
                        <span class="text-slate-300 font-mono">#{{ $enquiry->customer_id ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>

            <!-- Scheduled Inspection Card (if any) -->
            @if ($enquiry->inspection)
                <div class="p-6 rounded-2xl bg-slate-900 border border-emerald-800/80 space-y-3 shadow-xl">
                    <div class="flex items-center justify-between pb-2 border-b border-slate-800">
                        <span class="text-xs font-bold uppercase tracking-wider text-emerald-400">Site Inspection Booked</span>
                        <a href="{{ route('admin.inspections.show', $enquiry->inspection) }}" class="text-2xs text-slate-400 hover:text-white">View &rarr;</a>
                    </div>

                    <div class="space-y-1 text-xs">
                        <p class="font-bold text-white">{{ $enquiry->inspection->formatted_date }} at {{ $enquiry->inspection->scheduled_time }}</p>
                        <p class="text-slate-400 text-2xs">Meeting: {{ $enquiry->inspection->meeting_location }}</p>
                        <span class="inline-block mt-2 px-2 py-0.5 rounded text-3xs font-extrabold uppercase bg-emerald-950 text-emerald-300 border border-emerald-800">
                            Status: {{ \App\Models\Inspection::STATUSES[$enquiry->inspection->status] ?? $enquiry->inspection->status }}
                        </span>
                    </div>
                </div>
            @endif

        </div>

    </div>

</div>
@endsection

