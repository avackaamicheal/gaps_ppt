@extends('layouts.admin')

@section('title', 'Inspection Appointment: ' . $inspection->property->reference)
@section('header', 'Inspection Agenda & Brief')

@section('content')
<div class="space-y-6">

    <!-- Top Action Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <a href="{{ route('admin.inspections.index') }}" class="text-xs text-slate-400 hover:text-emerald-400 flex items-center gap-1 mb-2">
                &larr; Back to Inspections Schedule
            </a>
            <div class="flex items-center gap-3">
                <h2 class="text-2xl font-bold text-white tracking-tight">Site Inspection Brief</h2>
                @php
                    $statusStyle = match ($inspection->status) {
                        'confirmed' => 'bg-emerald-950 text-emerald-300 border-emerald-800',
                        'pending_confirmation' => 'bg-amber-950 text-amber-300 border-amber-800',
                        'rescheduled' => 'bg-purple-950 text-purple-300 border-purple-800',
                        'completed' => 'bg-blue-950 text-blue-300 border-blue-800',
                        'cancelled' => 'bg-rose-950 text-rose-300 border-rose-800',
                        default => 'bg-slate-800 text-slate-300 border-slate-700',
                    };
                @endphp
                <span class="px-2.5 py-0.5 rounded-full text-2xs font-extrabold uppercase tracking-wider border {{ $statusStyle }}">
                    {{ \App\Models\Inspection::STATUSES[$inspection->status] ?? $inspection->status }}
                </span>
            </div>
            <p class="text-xs text-slate-400 mt-1">
                Scheduled for <strong class="text-white">{{ $inspection->formatted_date }} at {{ $inspection->scheduled_time }}</strong>
            </p>
        </div>

        <div class="flex items-center gap-2">
            @php
                $cleanPhone = preg_replace('/[^0-9]/', '', $inspection->customer->phone);
                if (str_starts_with($cleanPhone, '0') && strlen($cleanPhone) === 11) {
                    $waPhone = '234' . substr($cleanPhone, 1);
                } else {
                    $waPhone = $cleanPhone;
                }
                $waText = "Hello {$inspection->customer->name}, Victor Akande here confirming our site inspection for [{$inspection->property->reference}] - {$inspection->property->title} on {$inspection->formatted_date} at {$inspection->scheduled_time}. Meeting rendezvous: {$inspection->meeting_location}. Please let me know once you are on your way!";
            @endphp
            <a href="https://wa.me/{{ $waPhone }}?text={{ urlencode($waText) }}" target="_blank"
               class="px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-lg shadow-emerald-950 transition-all flex items-center gap-2">
                <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
                    <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
                </svg>
                Send WhatsApp Confirmation
            </a>

            <a href="{{ route('admin.inspections.edit', $inspection) }}"
               class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-white font-semibold text-xs border border-slate-700 transition-colors">
                Reschedule
            </a>

            <form method="POST" action="{{ route('admin.inspections.destroy', $inspection) }}"
                  onsubmit="return confirm('Remove this inspection appointment?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="p-2.5 rounded-xl bg-slate-900 hover:bg-rose-950 text-slate-400 hover:text-rose-400 border border-slate-800 transition-colors cursor-pointer" title="Cancel & delete">
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

        <!-- Left 2 Cols: Logistics & Notes -->
        <div class="lg:col-span-2 space-y-6">

            <!-- Meeting Coordinates & Schedule Card -->
            <div class="p-6 sm:p-7 rounded-2xl bg-slate-900 border border-slate-800 space-y-5 shadow-xl">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 pb-3 border-b border-slate-800 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                    Inspection Logistics
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="p-4 rounded-xl bg-slate-950 border border-slate-800">
                        <span class="text-2xs uppercase tracking-wider text-slate-500 font-bold block mb-1">Date & Time</span>
                        <p class="text-base font-bold text-white">{{ $inspection->formatted_date }}</p>
                        <p class="text-xs text-emerald-400 font-mono mt-0.5">{{ $inspection->scheduled_time }}</p>
                    </div>

                    <div class="p-4 rounded-xl bg-slate-950 border border-slate-800">
                        <span class="text-2xs uppercase tracking-wider text-slate-500 font-bold block mb-1">Status</span>
                        <p class="text-base font-bold text-white capitalize">{{ \App\Models\Inspection::STATUSES[$inspection->status] ?? $inspection->status }}</p>
                        <p class="text-xs text-slate-400 mt-0.5">Appointment confirmation</p>
                    </div>
                </div>

                <div class="p-4 rounded-xl bg-slate-950 border border-slate-800 space-y-1">
                    <span class="text-2xs uppercase tracking-wider text-slate-500 font-bold block mb-1">Landmark Rendezvous Point</span>
                    <p class="text-sm font-bold text-white flex items-center gap-2">
                        <svg class="w-4 h-4 text-rose-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span>{{ $inspection->meeting_location }}</span>
                    </p>
                    <p class="text-xs text-slate-400 pt-1">
                        Physical location: {{ $inspection->property->display_location }}
                    </p>
                </div>

                @if ($inspection->notes)
                    <div class="p-4 rounded-xl bg-slate-950 border border-slate-800 space-y-1">
                        <span class="text-2xs uppercase tracking-wider text-amber-400 font-bold block mb-1">Preparation & Inspection Notes</span>
                        <p class="text-xs text-slate-200 leading-relaxed whitespace-pre-wrap">{{ $inspection->notes }}</p>
                    </div>
                @endif
            </div>

            <!-- Target Property Overview -->
            <div class="p-6 rounded-2xl bg-slate-900 border border-slate-800 space-y-4 shadow-xl">
                <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">
                        Target Property for Inspection
                    </h3>
                    <a href="{{ route('admin.properties.show', $inspection->property) }}" class="text-emerald-400 hover:underline text-2xs font-semibold">
                        View Property Listing &rarr;
                    </a>
                </div>

                <div class="flex items-center gap-4">
                    @if ($inspection->property->cover_url)
                        <img src="{{ $inspection->property->cover_url }}" class="w-24 h-20 rounded-xl object-cover bg-slate-950">
                    @else
                        <div class="w-24 h-20 rounded-xl bg-slate-950 flex items-center justify-center text-slate-700 text-xs">No Photo</div>
                    @endif

                    <div class="space-y-1">
                        <span class="font-mono text-2xs font-bold text-emerald-400">[{{ $inspection->property->reference }}]</span>
                        <h4 class="text-base font-bold text-white">{{ $inspection->property->title }}</h4>
                        <p class="text-xs text-slate-300 font-mono">{{ $inspection->property->formatted_price }}</p>
                        <p class="text-2xs text-slate-400">{{ $inspection->property->display_location }}</p>
                    </div>
                </div>
            </div>

        </div>

        <!-- Right 1 Col: Customer Details & Quick Contact -->
        <div class="space-y-6">

            <div class="p-6 rounded-2xl bg-slate-900 border border-slate-800 space-y-4 shadow-xl">
                <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">
                        Attendee Client
                    </h3>
                    <a href="{{ route('admin.customers.show', $inspection->customer) }}" class="text-emerald-400 hover:underline text-2xs font-semibold">
                        Client 360° &rarr;
                    </a>
                </div>

                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-tr from-amber-500 to-orange-400 flex items-center justify-center text-slate-950 font-black text-base shadow">
                        {{ strtoupper(substr($inspection->customer->name, 0, 2)) }}
                    </div>
                    <div>
                        <h4 class="font-bold text-white text-sm">{{ $inspection->customer->name }}</h4>
                        <span class="text-2xs text-slate-400">Client / Buyer</span>
                    </div>
                </div>

                <div class="space-y-2 text-xs pt-2">
                    <div class="flex items-center justify-between text-slate-400">
                        <span>Phone:</span>
                        <a href="tel:{{ $inspection->customer->phone }}" class="text-white font-mono hover:text-emerald-400">{{ $inspection->customer->phone }}</a>
                    </div>
                    @if ($inspection->customer->email)
                        <div class="flex items-center justify-between text-slate-400">
                            <span>Email:</span>
                            <a href="mailto:{{ $inspection->customer->email }}" class="text-white hover:text-emerald-400 truncate max-w-[180px]">{{ $inspection->customer->email }}</a>
                        </div>
                    @endif
                </div>

                <div class="pt-3 border-t border-slate-800 space-y-2">
                    <a href="https://wa.me/{{ $waPhone }}" target="_blank"
                       class="w-full py-2.5 px-3 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs flex items-center justify-center gap-2 transition-colors">
                        <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
                            <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
                        </svg>
                        Chat on WhatsApp
                    </a>

                    <a href="tel:{{ $inspection->customer->phone }}"
                       class="w-full py-2.5 px-3 rounded-xl bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs flex items-center justify-center gap-2 transition-colors">
                        <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                        </svg>
                        Direct Call
                    </a>
                </div>
            </div>

            <!-- Originating Enquiry (if any) -->
            @if ($inspection->enquiry)
                <div class="p-6 rounded-2xl bg-slate-900 border border-slate-800 space-y-2 shadow-xl">
                    <span class="text-2xs uppercase tracking-wider text-slate-500 font-bold block mb-1">Originating Lead Inquiry</span>
                    <p class="text-xs text-slate-300 line-clamp-3 italic">
                        &ldquo;{{ $inspection->enquiry->message }}&rdquo;
                    </p>
                    <a href="{{ route('admin.enquiries.show', $inspection->enquiry) }}" class="text-2xs text-emerald-400 hover:underline block pt-1">
                        View Original Enquiry &rarr;
                    </a>
                </div>
            @endif

        </div>

    </div>

</div>
@endsection

