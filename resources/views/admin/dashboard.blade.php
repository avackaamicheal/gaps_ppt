@extends('layouts.admin')

@section('title', 'Admin Overview')
@section('header', 'Operational Overview')

@section('content')
<div class="space-y-8">

    <!-- Welcome & Phase 2 Status Banner -->
    <div class="p-6 sm:p-8 rounded-2xl bg-gradient-to-r from-emerald-950/80 via-slate-900 to-slate-900 border border-emerald-500/30 shadow-xl">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-300 text-xs font-semibold uppercase tracking-wider mb-3">
                    <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                    Phase 2 Inventory & Discovery Active
                </div>
                <h2 class="text-2xl sm:text-3xl font-extrabold text-white">
                    Welcome back, {{ auth()->user()->name }}
                </h2>
                <p class="text-sm text-slate-300 mt-2 max-w-2xl leading-relaxed">
                    Property inventory management, concurrent-safe reference generation, private document vault, and public discovery are live.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('admin.properties.create') }}" class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-semibold text-xs shadow-lg shadow-emerald-950 transition-all flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Add Property
                </a>
                <a href="{{ route('properties.index') }}" target="_blank" class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold text-xs border border-slate-700 transition-all">
                    Public Listings &rarr;
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
            <span class="text-xs text-emerald-400 font-semibold">● Active Inventory</span>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
            <!-- Total Properties -->
            <div class="p-5 rounded-2xl bg-slate-900 border border-slate-800 shadow-xs">
                <div class="flex items-center justify-between text-slate-400 mb-2">
                    <span class="text-2xs font-bold uppercase tracking-wider">Total</span>
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

            <!-- Draft Properties -->
            <div class="p-5 rounded-2xl bg-slate-900 border border-slate-800 shadow-xs">
                <div class="flex items-center justify-between text-slate-400 mb-2">
                    <span class="text-2xs font-bold uppercase tracking-wider">Drafts</span>
                    <span class="p-1.5 rounded-lg bg-slate-800 text-slate-400">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                        </svg>
                    </span>
                </div>
                <span class="text-3xl font-extrabold text-slate-300 font-mono">{{ $stats['draft_properties'] }}</span>
            </div>

            <!-- Under Negotiation -->
            <div class="p-5 rounded-2xl bg-slate-900 border border-slate-800 shadow-xs">
                <div class="flex items-center justify-between text-slate-400 mb-2">
                    <span class="text-2xs font-bold uppercase tracking-wider">Negotiation</span>
                    <span class="p-1.5 rounded-lg bg-amber-950/60 text-amber-400">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        </svg>
                    </span>
                </div>
                <span class="text-3xl font-extrabold text-amber-300 font-mono">{{ $stats['under_negotiation'] }}</span>
            </div>

            <!-- Sold Properties -->
            <div class="p-5 rounded-2xl bg-slate-900 border border-slate-800 shadow-xs">
                <div class="flex items-center justify-between text-slate-400 mb-2">
                    <span class="text-2xs font-bold uppercase tracking-wider">Sold</span>
                    <span class="p-1.5 rounded-lg bg-rose-950/60 text-rose-400">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </span>
                </div>
                <span class="text-3xl font-extrabold text-rose-300 font-mono">{{ $stats['sold_properties'] }}</span>
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
                <p class="text-xs text-slate-400 mt-1">Schema, unique references, image gallery, private documents, public search & filtering.</p>
            </div>

            <!-- Phase 3 -->
            <div class="p-4 rounded-xl bg-slate-950/60 border border-slate-800">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-bold text-slate-400">Phase 3</span>
                    <span class="text-2xs font-extrabold px-1.5 py-0.5 rounded-sm bg-slate-800 text-slate-400">NEXT</span>
                </div>
                <h4 class="text-sm font-semibold text-slate-200">Leads & Inspections</h4>
                <p class="text-xs text-slate-500 mt-1">Website enquiries, manual phone/WhatsApp lead entry, customer records, and inspection scheduling.</p>
            </div>

            <!-- Phase 4 -->
            <div class="p-4 rounded-xl bg-slate-950/60 border border-slate-800">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-bold text-slate-400">Phase 4</span>
                    <span class="text-2xs font-extrabold px-1.5 py-0.5 rounded-sm bg-slate-800 text-slate-500">PENDING</span>
                </div>
                <h4 class="text-sm font-semibold text-slate-300">Content & Launch</h4>
                <p class="text-xs text-slate-500 mt-1">Testimonials, site settings, activity logs, SEO metadata, performance audits, and deployment.</p>
            </div>
        </div>
    </div>

</div>
@endsection
