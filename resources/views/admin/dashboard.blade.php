@extends('layouts.admin')

@section('title', 'Admin Overview')
@section('header', 'Operational Overview')

@section('content')
<div class="space-y-8">

    <!-- Welcome & Phase 3 Status Banner -->
    <div class="p-6 sm:p-8 rounded-2xl bg-gradient-to-r from-emerald-950/80 via-slate-900 to-slate-900 border border-emerald-500/30 shadow-xl">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-300 text-xs font-semibold uppercase tracking-wider mb-3">
                    <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                    Phase 3 Lead Capture, CRM & Inspection Scheduling Active
                </div>
                <h2 class="text-2xl sm:text-3xl font-extrabold text-white">
                    Welcome back, {{ auth()->user()->name }}
                </h2>
                <p class="text-sm text-slate-300 mt-2 max-w-2xl leading-relaxed">
                    Website inquiries, offline WhatsApp/phone lead capture, client CRM profiles, and site inspection management are operational.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('admin.enquiries.create') }}" class="px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-semibold text-xs shadow-lg shadow-emerald-950 transition-all flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Record Offline Lead
                </a>
                <a href="{{ route('admin.inspections.create') }}" class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 font-semibold text-xs border border-slate-700 transition-all flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    Schedule Inspection
                </a>
            </div>
        </div>
    </div>

    <!-- Live Metric Cards -->
    <div>
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-bold text-slate-300 uppercase tracking-wider">
                System Metrics (Live Database Records)
            </h3>
            <span class="text-xs text-emerald-400 font-semibold">● Real-Time Agency Operations</span>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
            <!-- Total Properties -->
            <div class="p-5 rounded-2xl bg-slate-900 border border-slate-800 shadow-xs">
                <div class="flex items-center justify-between text-slate-400 mb-2">
                    <span class="text-2xs font-bold uppercase tracking-wider">Properties</span>
                    <span class="p-1.5 rounded-lg bg-slate-800 text-slate-300">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </span>
                </div>
                <span class="text-3xl font-extrabold text-white font-mono">{{ $stats['total_properties'] }}</span>
            </div>

            <!-- Published Properties -->
            <div class="p-5 rounded-2xl bg-slate-900 border border-slate-800 shadow-xs">
                <div class="flex items-center justify-between text-slate-400 mb-2">
                    <span class="text-2xs font-bold uppercase tracking-wider">Published</span>
                    <span class="p-1.5 rounded-lg bg-emerald-950/60 text-emerald-400">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </span>
                </div>
                <span class="text-3xl font-extrabold text-emerald-400 font-mono">{{ $stats['published_properties'] }}</span>
            </div>

            <!-- New Inquiries -->
            <div class="p-5 rounded-2xl bg-slate-900 border {{ $stats['new_enquiries'] > 0 ? 'border-rose-800 ring-2 ring-rose-500/30' : 'border-slate-800' }} shadow-xs">
                <div class="flex items-center justify-between text-slate-400 mb-2">
                    <span class="text-2xs font-bold uppercase tracking-wider">New Leads</span>
                    <span class="p-1.5 rounded-lg bg-rose-950/60 text-rose-400">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                        </svg>
                    </span>
                </div>
                <span class="text-3xl font-extrabold {{ $stats['new_enquiries'] > 0 ? 'text-rose-400' : 'text-slate-300' }} font-mono">{{ $stats['new_enquiries'] }}</span>
            </div>

            <!-- Upcoming Inspections -->
            <div class="p-5 rounded-2xl bg-slate-900 border {{ $stats['upcoming_inspections'] > 0 ? 'border-amber-800 ring-2 ring-amber-500/30' : 'border-slate-800' }} shadow-xs">
                <div class="flex items-center justify-between text-slate-400 mb-2">
                    <span class="text-2xs font-bold uppercase tracking-wider">Inspections</span>
                    <span class="p-1.5 rounded-lg bg-amber-950/60 text-amber-400">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </span>
                </div>
                <span class="text-3xl font-extrabold text-amber-400 font-mono">{{ $stats['upcoming_inspections'] }}</span>
            </div>

            <!-- Total Clients in CRM -->
            <div class="p-5 rounded-2xl bg-slate-900 border border-slate-800 shadow-xs">
                <div class="flex items-center justify-between text-slate-400 mb-2">
                    <span class="text-2xs font-bold uppercase tracking-wider">Clients CRM</span>
                    <span class="p-1.5 rounded-lg bg-sky-950/60 text-sky-400">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </span>
                </div>
                <span class="text-3xl font-extrabold text-sky-400 font-mono">{{ $stats['total_customers'] }}</span>
            </div>

            <!-- Sold Properties -->
            <div class="p-5 rounded-2xl bg-slate-900 border border-slate-800 shadow-xs">
                <div class="flex items-center justify-between text-slate-400 mb-2">
                    <span class="text-2xs font-bold uppercase tracking-wider">Sold</span>
                    <span class="p-1.5 rounded-lg bg-slate-800 text-slate-400">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </span>
                </div>
                <span class="text-3xl font-extrabold text-slate-300 font-mono">{{ $stats['sold_properties'] }}</span>
            </div>
        </div>
    </div>

    <!-- Recent Properties Table -->
    <div class="p-6 rounded-2xl bg-slate-900 border border-slate-800 space-y-4">
        <div class="flex items-center justify-between">
            <h3 class="text-base font-bold text-white">Recently Added Properties</h3>
            <a href="{{ route('admin.properties.index') }}" class="text-xs text-emerald-400 hover:text-emerald-300 font-semibold">
                View All &rarr;
            </a>
        </div>

        @if ($recentProperties->isEmpty())
            <div class="p-8 text-center text-xs text-slate-500">
                No properties added yet. <a href="{{ route('admin.properties.create') }}" class="text-emerald-400 font-bold hover:underline">Add your first property</a>.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="text-slate-400 uppercase tracking-wider text-2xs border-b border-slate-800 pb-2">
                        <tr>
                            <th class="py-2.5">Property</th>
                            <th class="py-2.5">Category</th>
                            <th class="py-2.5">Price</th>
                            <th class="py-2.5">Status</th>
                            <th class="py-2.5 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/60 text-slate-300">
                        @foreach ($recentProperties as $prop)
                            <tr>
                                <td class="py-3">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-lg bg-slate-800 overflow-hidden shrink-0 flex items-center justify-center">
                                            @if ($prop->cover_url)
                                                <img src="{{ $prop->cover_url }}" class="w-full h-full object-cover">
                                            @else
                                                <span class="text-slate-600 text-2xs">N/A</span>
                                            @endif
                                        </div>
                                        <div>
                                            <a href="{{ route('admin.properties.show', $prop) }}" class="font-bold text-white hover:text-emerald-400">
                                                {{ $prop->title }}
                                            </a>
                                            <p class="text-2xs text-slate-500 font-mono">{{ $prop->reference }} • {{ $prop->display_location }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3">
                                    <span class="text-2xs font-semibold px-2 py-0.5 rounded-full {{ $prop->isLand() ? 'bg-teal-950 text-teal-300' : 'bg-sky-950 text-sky-300' }}">
                                        {{ $prop->category->name }}
                                    </span>
                                </td>
                                <td class="py-3 font-mono font-bold text-emerald-400">
                                    {{ $prop->formatted_price }}
                                </td>
                                <td class="py-3">
                                    <span class="px-2 py-0.5 rounded text-2xs font-bold uppercase
                                        @if($prop->status === 'published') bg-emerald-950 text-emerald-300
                                        @elseif($prop->status === 'draft') bg-slate-800 text-slate-300
                                        @else bg-amber-950 text-amber-300 @endif">
                                        {{ \App\Models\Property::STATUSES[$prop->status] ?? $prop->status }}
                                    </span>
                                </td>
                                <td class="py-3 text-right">
                                    <a href="{{ route('admin.properties.edit', $prop) }}" class="text-xs text-slate-400 hover:text-emerald-400 font-medium">
                                        Edit
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <!-- Enquiries & Inspections Split Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- Recent Inbound Leads -->
        <div class="p-6 rounded-2xl bg-slate-900 border border-slate-800 space-y-4 shadow-xl">
            <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-rose-400"></span>
                    <h3 class="text-sm font-bold text-white uppercase tracking-wider">Recent Inbound Leads</h3>
                </div>
                <a href="{{ route('admin.enquiries.index') }}" class="text-xs text-emerald-400 hover:text-emerald-300 font-semibold">
                    View All Leads &rarr;
                </a>
            </div>

            <div class="space-y-3">
                @forelse ($recentEnquiries as $enq)
                    <div class="p-3.5 rounded-xl bg-slate-950 border border-slate-800 flex items-start justify-between gap-3">
                        <div class="space-y-1 min-w-0">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('admin.enquiries.show', $enq) }}" class="text-xs font-bold text-white hover:text-emerald-400 truncate">
                                    {{ $enq->name }}
                                </a>
                                <span class="px-1.5 py-0.2 rounded text-3xs font-extrabold uppercase bg-slate-800 text-slate-300">
                                    {{ $enq->source }}
                                </span>
                            </div>
                            @if ($enq->property)
                                <p class="text-2xs text-slate-400 font-mono truncate">
                                    [{{ $enq->property->reference }}] {{ $enq->property->title }}
                                </p>
                            @endif
                            <p class="text-2xs text-slate-400 line-clamp-1 italic">
                                &ldquo;{{ $enq->message }}&rdquo;
                            </p>
                        </div>

                        <div class="text-right shrink-0">
                            <span class="text-3xs text-slate-500 block">{{ $enq->created_at->diffForHumans() }}</span>
                            <a href="{{ route('admin.enquiries.show', $enq) }}" class="text-2xs text-emerald-400 hover:underline font-semibold block mt-1">
                                View &rarr;
                            </a>
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-slate-500 py-6 text-center">No inbound leads recorded yet.</p>
                @endforelse
            </div>
        </div>

        <!-- Upcoming Inspections Schedule -->
        <div class="p-6 rounded-2xl bg-slate-900 border border-slate-800 space-y-4 shadow-xl">
            <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-amber-400"></span>
                    <h3 class="text-sm font-bold text-white uppercase tracking-wider">Upcoming Inspections</h3>
                </div>
                <a href="{{ route('admin.inspections.index') }}" class="text-xs text-emerald-400 hover:text-emerald-300 font-semibold">
                    View Agenda &rarr;
                </a>
            </div>

            <div class="space-y-3">
                @forelse ($upcomingInspections as $insp)
                    <div class="p-3.5 rounded-xl bg-slate-950 border border-slate-800 flex items-start justify-between gap-3">
                        <div class="space-y-1 min-w-0">
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-bold text-white">{{ $insp->formatted_date }} at {{ $insp->scheduled_time }}</span>
                                <span class="px-1.5 py-0.2 rounded text-3xs font-extrabold uppercase bg-amber-950 text-amber-300 border border-amber-800">
                                    {{ $insp->status }}
                                </span>
                            </div>
                            <p class="text-2xs text-slate-400 font-mono truncate">
                                [{{ $insp->property->reference }}] {{ $insp->property->title }}
                            </p>
                            <p class="text-2xs text-slate-400 truncate">
                                Client: <strong class="text-slate-300">{{ $insp->customer->name }}</strong> • Meeting: {{ $insp->meeting_location }}
                            </p>
                        </div>

                        <div class="text-right shrink-0">
                            <a href="{{ route('admin.inspections.show', $insp) }}" class="text-2xs text-emerald-400 hover:underline font-semibold block mt-1">
                                Agenda &rarr;
                            </a>
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-slate-500 py-6 text-center">No upcoming site visits scheduled.</p>
                @endforelse
            </div>
        </div>

    </div>

    <!-- Phased Rollout Roadmap -->
    <div class="p-6 rounded-2xl bg-slate-900 border border-slate-800">
        <h3 class="text-base font-bold text-white mb-2">Development Roadmap Progress</h3>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-4">
            <!-- Phase 1 -->
            <div class="p-4 rounded-xl bg-emerald-950/40 border border-emerald-500/40">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-bold text-emerald-400">Phase 1</span>
                    <span class="text-2xs font-extrabold px-1.5 py-0.5 rounded-sm bg-emerald-500/20 text-emerald-300">COMPLETE</span>
                </div>
                <h4 class="text-sm font-semibold text-white">Application Foundation</h4>
                <p class="text-xs text-slate-400 mt-1">Laravel foundation, admin auth, session security, public shell.</p>
            </div>

            <!-- Phase 2 -->
            <div class="p-4 rounded-xl bg-emerald-950/40 border border-emerald-500/40">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-bold text-emerald-400">Phase 2</span>
                    <span class="text-2xs font-extrabold px-1.5 py-0.5 rounded-sm bg-emerald-500/20 text-emerald-300">COMPLETE</span>
                </div>
                <h4 class="text-sm font-semibold text-white">Property Management</h4>
                <p class="text-xs text-slate-400 mt-1">Schema, unique references, luxury image manager, private documents, public search & filtering.</p>
            </div>

            <!-- Phase 3 -->
            <div class="p-4 rounded-xl bg-emerald-950/40 border border-emerald-500/40">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-bold text-emerald-400">Phase 3</span>
                    <span class="text-2xs font-extrabold px-1.5 py-0.5 rounded-sm bg-emerald-500/20 text-emerald-300">COMPLETE</span>
                </div>
                <h4 class="text-sm font-semibold text-white">Leads & Inspections</h4>
                <p class="text-xs text-slate-400 mt-1">Website enquiries, manual phone/WhatsApp lead entry, customer CRM, and inspection scheduling.</p>
            </div>

            <!-- Phase 4 -->
            <div class="p-4 rounded-xl bg-slate-950/60 border border-slate-800">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-bold text-slate-400">Phase 4</span>
                    <span class="text-2xs font-extrabold px-1.5 py-0.5 rounded-sm bg-slate-800 text-slate-400">NEXT</span>
                </div>
                <h4 class="text-sm font-semibold text-slate-200">Content & Launch</h4>
                <p class="text-xs text-slate-500 mt-1">Testimonials, site settings, activity logs, SEO metadata, performance audits, and deployment.</p>
            </div>
        </div>
    </div>

</div>
@endsection
