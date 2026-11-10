@extends('layouts.admin')

@section('title', 'Admin Overview')
@section('header', 'Operational Overview')

@section('content')
<div class="space-y-8">

    <!-- Welcome & Phase 1 Status Banner -->
    <div class="p-6 sm:p-8 rounded-2xl bg-gradient-to-r from-emerald-950/80 via-slate-900 to-slate-900 border border-emerald-500/30 shadow-xl">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-300 text-xs font-semibold uppercase tracking-wider mb-3">
                    <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                    Phase 1 Foundation Active
                </div>
                <h2 class="text-2xl sm:text-3xl font-extrabold text-white">
                    Welcome back, {{ auth()->user()->name }}
                </h2>
                <p class="text-sm text-slate-300 mt-2 max-w-2xl leading-relaxed">
                    The core infrastructure, secure single-admin authentication, session hardening, and public homepage shell are fully active. Property inventory management will unlock in Phase 2.
                </p>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('home') }}" target="_blank" class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-semibold text-xs shadow-lg shadow-emerald-950 transition-all">
                    Preview Public Homepage &rarr;
                </a>
            </div>
        </div>
    </div>

    <!-- Live Metric Cards (Reflecting actual Phase 1 zero state — No fake stats) -->
    <div>
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-bold text-slate-300 uppercase tracking-wider">
                System Metrics (Live Database Records)
            </h3>
            <span class="text-xs text-slate-500">Zero mock data policy</span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            <!-- Properties Total -->
            <div class="p-5 rounded-2xl bg-slate-900 border border-slate-800 shadow-xs">
                <div class="flex items-center justify-between text-slate-400 mb-3">
                    <span class="text-xs font-medium uppercase tracking-wider">Total Properties</span>
                    <span class="p-1.5 rounded-lg bg-slate-800 text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </span>
                </div>
                <div class="flex items-baseline gap-2">
                    <span class="text-3xl font-extrabold text-white">{{ $stats['total_properties'] }}</span>
                    <span class="text-xs text-slate-500">(Unlocks Phase 2)</span>
                </div>
            </div>

            <!-- Published Properties -->
            <div class="p-5 rounded-2xl bg-slate-900 border border-slate-800 shadow-xs">
                <div class="flex items-center justify-between text-slate-400 mb-3">
                    <span class="text-xs font-medium uppercase tracking-wider">Published Listings</span>
                    <span class="p-1.5 rounded-lg bg-emerald-950/60 text-emerald-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </span>
                </div>
                <div class="flex items-baseline gap-2">
                    <span class="text-3xl font-extrabold text-white">{{ $stats['published_properties'] }}</span>
                    <span class="text-xs text-slate-500">(Unlocks Phase 2)</span>
                </div>
            </div>

            <!-- New Enquiries -->
            <div class="p-5 rounded-2xl bg-slate-900 border border-slate-800 shadow-xs">
                <div class="flex items-center justify-between text-slate-400 mb-3">
                    <span class="text-xs font-medium uppercase tracking-wider">New Enquiries</span>
                    <span class="p-1.5 rounded-lg bg-slate-800 text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                        </svg>
                    </span>
                </div>
                <div class="flex items-baseline gap-2">
                    <span class="text-3xl font-extrabold text-white">{{ $stats['new_enquiries'] }}</span>
                    <span class="text-xs text-slate-500">(Unlocks Phase 3)</span>
                </div>
            </div>

            <!-- Upcoming Inspections -->
            <div class="p-5 rounded-2xl bg-slate-900 border border-slate-800 shadow-xs">
                <div class="flex items-center justify-between text-slate-400 mb-3">
                    <span class="text-xs font-medium uppercase tracking-wider">Upcoming Inspections</span>
                    <span class="p-1.5 rounded-lg bg-slate-800 text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </span>
                </div>
                <div class="flex items-baseline gap-2">
                    <span class="text-3xl font-extrabold text-white">{{ $stats['upcoming_inspections'] }}</span>
                    <span class="text-xs text-slate-500">(Unlocks Phase 3)</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Phased Rollout Roadmap -->
    <div class="p-6 rounded-2xl bg-slate-900 border border-slate-800">
        <h3 class="text-base font-bold text-white mb-2">Development Roadmap Progress</h3>
        <p class="text-xs text-slate-400 mb-6 leading-relaxed">
            Strict phase-by-phase delivery model ensures each component is tested and verified before expanding scope.
        </p>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Phase 1 -->
            <div class="p-4 rounded-xl bg-emerald-950/40 border border-emerald-500/40">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-bold text-emerald-400">Phase 1</span>
                    <span class="text-2xs font-extrabold px-1.5 py-0.5 rounded-sm bg-emerald-500/20 text-emerald-300">COMPLETE</span>
                </div>
                <h4 class="text-sm font-semibold text-white">Application Foundation</h4>
                <p class="text-xs text-slate-400 mt-1">Laravel, MySQL, Vite, Tailwind CSS, Alpine.js, single-admin auth, session security & layouts.</p>
            </div>

            <!-- Phase 2 -->
            <div class="p-4 rounded-xl bg-slate-950/60 border border-slate-800">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-bold text-slate-400">Phase 2</span>
                    <span class="text-2xs font-extrabold px-1.5 py-0.5 rounded-sm bg-slate-800 text-slate-400">NEXT</span>
                </div>
                <h4 class="text-sm font-semibold text-slate-200">Property Management</h4>
                <p class="text-xs text-slate-500 mt-1">Schema, property categories, references, image uploads, private documents, public search & filtering.</p>
            </div>

            <!-- Phase 3 -->
            <div class="p-4 rounded-xl bg-slate-950/60 border border-slate-800">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-bold text-slate-400">Phase 3</span>
                    <span class="text-2xs font-extrabold px-1.5 py-0.5 rounded-sm bg-slate-800 text-slate-500">PENDING</span>
                </div>
                <h4 class="text-sm font-semibold text-slate-300">Leads & Inspections</h4>
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
