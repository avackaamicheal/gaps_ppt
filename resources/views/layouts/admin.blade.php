<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-900 text-slate-100 antialiased">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Admin Dashboard') — {{ config('app.name', 'Real Estate Platform') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full flex bg-slate-950 font-sans text-slate-100" x-data="{ sidebarOpen: false }">

    <!-- Mobile Sidebar Backdrop -->
    <div x-show="sidebarOpen"
         x-cloak
         @click="sidebarOpen = false"
         class="fixed inset-0 z-40 bg-slate-950/80 backdrop-blur-xs lg:hidden">
    </div>

    <!-- Sidebar Navigation -->
    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
           class="fixed inset-y-0 left-0 z-50 w-72 bg-slate-900 border-r border-slate-800 transition-transform duration-200 ease-in-out lg:static lg:translate-x-0 flex flex-col justify-between">

        <div>
            <!-- Brand / Logo -->
            <div class="h-20 flex items-center px-6 border-b border-slate-800">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-emerald-500 to-teal-400 flex items-center justify-center text-slate-950 font-black shadow-md shadow-emerald-500/20">
                        VA
                    </div>
                    <div>
                        <span class="block text-sm font-bold text-white tracking-tight">Victor Akande</span>
                        <span class="block text-2xs font-semibold text-emerald-400 uppercase tracking-widest">Admin Control</span>
                    </div>
                </a>
            </div>

            <!-- Navigation Links -->
            <nav class="px-4 py-6 space-y-1.5 text-sm">
                <!-- 1. Overview (Functional in Phase 1) -->
                <a href="{{ route('admin.dashboard') }}"
                   class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-semibold transition-colors {{ request()->routeIs('admin.dashboard') ? 'bg-emerald-600/20 text-emerald-400 border border-emerald-500/30' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    <span>Overview</span>
                    <span class="ml-auto text-2xs font-bold px-1.5 py-0.5 rounded-md bg-emerald-500/20 text-emerald-400">Live</span>
                </a>

                <!-- 2. Properties (Active in Phase 2) -->
                <a href="{{ route('admin.properties.index') }}"
                   class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-semibold transition-colors {{ request()->routeIs('admin.properties.*') ? 'bg-emerald-600/20 text-emerald-400 border border-emerald-500/30' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    <span>Properties</span>
                    <span class="ml-auto text-2xs font-bold px-1.5 py-0.5 rounded-md bg-emerald-500/20 text-emerald-400">Live</span>
                </a>

                <!-- 3. Enquiries (Active in Phase 3) -->
                @php
                    $navNewEnquiriesCount = \App\Models\Enquiry::where('status', \App\Models\Enquiry::STATUS_NEW)->count();
                @endphp
                <a href="{{ route('admin.enquiries.index') }}"
                   class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-semibold transition-colors {{ request()->routeIs('admin.enquiries.*') ? 'bg-emerald-600/20 text-emerald-400 border border-emerald-500/30' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                    </svg>
                    <span>Enquiries</span>
                    @if ($navNewEnquiriesCount > 0)
                        <span class="ml-auto text-2xs font-extrabold px-2 py-0.5 rounded-full bg-rose-500 text-white animate-pulse">
                            {{ $navNewEnquiriesCount }} new
                        </span>
                    @else
                        <span class="ml-auto text-2xs font-bold px-1.5 py-0.5 rounded-md bg-emerald-500/20 text-emerald-400">Live</span>
                    @endif
                </a>

                <!-- 4. Customers (Active in Phase 3) -->
                <a href="{{ route('admin.customers.index') }}"
                   class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-semibold transition-colors {{ request()->routeIs('admin.customers.*') ? 'bg-emerald-600/20 text-emerald-400 border border-emerald-500/30' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <span>Customers</span>
                    <span class="ml-auto text-2xs font-bold px-1.5 py-0.5 rounded-md bg-emerald-500/20 text-emerald-400">Live</span>
                </a>

                <!-- 5. Inspections (Active in Phase 3) -->
                @php
                    $navUpcomingCount = \App\Models\Inspection::upcoming()->count();
                @endphp
                <a href="{{ route('admin.inspections.index') }}"
                   class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-semibold transition-colors {{ request()->routeIs('admin.inspections.*') ? 'bg-emerald-600/20 text-emerald-400 border border-emerald-500/30' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span>Inspections</span>
                    @if ($navUpcomingCount > 0)
                        <span class="ml-auto text-2xs font-extrabold px-2 py-0.5 rounded-full bg-amber-500/20 text-amber-400 border border-amber-500/30">
                            {{ $navUpcomingCount }}
                        </span>
                    @else
                        <span class="ml-auto text-2xs font-bold px-1.5 py-0.5 rounded-md bg-emerald-500/20 text-emerald-400">Live</span>
                    @endif
                </a>

                <!-- 6. Testimonials (Phase 4) -->
                @php
                    $navTestimonialsCount = \App\Models\Testimonial::count();
                @endphp
                <a href="{{ route('admin.testimonials.index') }}"
                   class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-semibold transition-colors {{ request()->routeIs('admin.testimonials.*') ? 'bg-emerald-600/20 text-emerald-400 border border-emerald-500/30' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                    </svg>
                    <span>Testimonials</span>
                    @if ($navTestimonialsCount > 0)
                        <span class="ml-auto text-2xs font-bold px-1.5 py-0.5 rounded-md bg-emerald-500/20 text-emerald-400">{{ $navTestimonialsCount }}</span>
                    @endif
                </a>

                <!-- 7. Website Settings (Phase 4) -->
                <a href="{{ route('admin.settings.edit') }}"
                   class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-semibold transition-colors {{ request()->routeIs('admin.settings.*') ? 'bg-emerald-600/20 text-emerald-400 border border-emerald-500/30' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <span>Settings</span>
                </a>

                <!-- 8. Activity Logs (Phase 4) -->
                <a href="{{ route('admin.activity-logs.index') }}"
                   class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-semibold transition-colors {{ request()->routeIs('admin.activity-logs.*') ? 'bg-emerald-600/20 text-emerald-400 border border-emerald-500/30' : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Activity Logs</span>
                </a>
            </nav>
        </div>

        <!-- Sidebar User Footer -->
        <div class="p-4 border-t border-slate-800">
            <div class="flex items-center justify-between gap-3 p-2 rounded-xl bg-slate-950/60 border border-slate-800/80">
                <div class="flex items-center gap-2.5 min-w-0">
                    <div class="w-8 h-8 rounded-lg bg-emerald-600/30 text-emerald-400 flex items-center justify-center font-bold text-xs shrink-0">
                        {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs font-semibold text-white truncate">{{ auth()->user()->name ?? 'Administrator' }}</p>
                        <p class="text-2xs text-slate-400 truncate">{{ auth()->user()->email ?? 'admin@example.com' }}</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button type="submit" title="Sign Out" class="p-1.5 text-slate-400 hover:text-rose-400 hover:bg-rose-950/30 rounded-lg transition-colors cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                    </button>
                </form>
            </div>
        </div>

    </aside>

    <!-- Main Content Layout -->
    <div class="flex-1 flex flex-col min-w-0 min-h-screen">

        <!-- Top Header Bar -->
        <header class="h-20 bg-slate-900 border-b border-slate-800 px-4 sm:px-8 flex items-center justify-between sticky top-0 z-30">
            <div class="flex items-center gap-4">
                <button type="button" @click="sidebarOpen = true" class="lg:hidden p-2 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>

                <h1 class="text-lg font-bold text-white tracking-tight">
                    @yield('header', 'Admin Dashboard')
                </h1>
            </div>

            <div class="flex items-center gap-4">
                <a href="{{ route('home') }}" target="_blank" class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg bg-slate-800 text-slate-300 hover:text-white hover:bg-slate-700 border border-slate-700 transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                    </svg>
                    View Public Website
                </a>

                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg bg-rose-950/60 text-rose-300 hover:bg-rose-900/80 border border-rose-800/80 transition-colors cursor-pointer">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        Sign Out
                    </button>
                </form>
            </div>
        </header>

        <!-- Main Workspace Area -->
        <main class="flex-1 p-4 sm:p-8 max-w-7xl w-full mx-auto">
            <!-- Flash Notification -->
            @if (session('status') || session('success'))
                <div class="mb-6 p-4 rounded-xl bg-emerald-950/60 border border-emerald-800 text-emerald-300 text-sm flex items-center gap-3">
                    <svg class="w-5 h-5 text-emerald-400 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <span>{{ session('status') ?? session('success') }}</span>
                </div>
            @endif

            @yield('content')
        </main>

    </div>

</body>
</html>

