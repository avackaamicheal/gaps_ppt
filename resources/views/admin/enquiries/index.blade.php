@extends('layouts.admin')

@section('title', 'Property Enquiries & Leads')
@section('header', 'Enquiries & Lead Management')

@section('content')
<div class="space-y-6">

    <!-- Header Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-white tracking-tight">Enquiries & Inbound Leads</h2>
            <p class="text-xs text-slate-400 mt-1">
                Monitor incoming website enquiries, capture offline WhatsApp or phone calls, and schedule inspections.
            </p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('admin.enquiries.create') }}"
               class="px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-lg shadow-emerald-950 transition-all flex items-center gap-2 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Record Offline Lead (WhatsApp / Call)
            </a>
        </div>
    </div>

    <!-- Status Tabs -->
    <div class="flex items-center gap-2 overflow-x-auto pb-2 border-b border-slate-800 text-xs font-semibold">
        <a href="{{ route('admin.enquiries.index', array_merge(request()->except(['status', 'page']))) }}"
           class="px-3.5 py-1.5 rounded-lg transition-colors flex items-center gap-2 {{ !request('status') ? 'bg-emerald-600 text-white' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">
            <span>All Leads</span>
            <span class="px-1.5 py-0.2 rounded-full text-3xs font-mono {{ !request('status') ? 'bg-emerald-800 text-white' : 'bg-slate-800 text-slate-400' }}">{{ $statusCounts['all'] }}</span>
        </a>

        @foreach (\App\Models\Enquiry::STATUSES as $code => $label)
            <a href="{{ route('admin.enquiries.index', array_merge(request()->except('page'), ['status' => $code])) }}"
               class="px-3.5 py-1.5 rounded-lg transition-colors flex items-center gap-2 {{ request('status') === $code ? 'bg-emerald-600 text-white' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">
                <span>{{ $label }}</span>
                @if (($statusCounts[$code] ?? 0) > 0)
                    <span class="px-1.5 py-0.2 rounded-full text-3xs font-mono {{ request('status') === $code ? 'bg-emerald-800 text-white' : 'bg-slate-800 text-slate-400' }}">
                        {{ $statusCounts[$code] }}
                    </span>
                @endif
            </a>
        @endforeach
    </div>

    <!-- Filters Bar -->
    <div class="p-4 rounded-2xl bg-slate-900 border border-slate-800">
        <form method="GET" action="{{ route('admin.enquiries.index') }}" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3 items-center">
            @if (request('status'))
                <input type="hidden" name="status" value="{{ request('status') }}">
            @endif

            <!-- Keyword Search -->
            <div>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Search name, phone, email, notes..."
                       class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-xs text-white placeholder-slate-500 focus:outline-hidden focus:border-emerald-500">
            </div>

            <!-- Source Filter -->
            <div>
                <select name="source" class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-xs text-white focus:outline-hidden focus:border-emerald-500">
                    <option value="">All Lead Sources</option>
                    @foreach (\App\Models\Enquiry::SOURCES as $sourceCode => $sourceLabel)
                        <option value="{{ $sourceCode }}" {{ request('source') === $sourceCode ? 'selected' : '' }}>
                            {{ $sourceLabel }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Property Filter -->
            <div>
                <select name="property_id" class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-xs text-white focus:outline-hidden focus:border-emerald-500 truncate">
                    <option value="">All Referenced Properties</option>
                    @foreach ($properties as $prop)
                        <option value="{{ $prop->id }}" {{ request('property_id') == $prop->id ? 'selected' : '' }}>
                            [{{ $prop->reference }}] {{ Str::limit($prop->title, 28) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-center gap-2">
                <button type="submit" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-white font-semibold text-xs transition-colors cursor-pointer">
                    Apply Filter
                </button>
                @if (request()->hasAny(['search', 'source', 'property_id', 'status']))
                    <a href="{{ route('admin.enquiries.index') }}" class="text-xs text-slate-400 hover:text-rose-400">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Enquiries Data Table -->
    <div class="rounded-2xl bg-slate-900 border border-slate-800 overflow-hidden shadow-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="border-b border-slate-800 bg-slate-950/60 text-2xs uppercase tracking-wider text-slate-400 font-bold">
                        <th class="py-3 px-4">Contact / Lead</th>
                        <th class="py-3 px-4">Source</th>
                        <th class="py-3 px-4">Property of Interest</th>
                        <th class="py-3 px-4">Initial Message / Notes</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4">Date</th>
                        <th class="py-3 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800 text-slate-300">
                    @forelse ($enquiries as $enquiry)
                        <tr class="hover:bg-slate-800/40 transition-colors">
                            <!-- Contact -->
                            <td class="py-3.5 px-4">
                                <div class="font-bold text-white flex items-center gap-1.5">
                                    <a href="{{ route('admin.enquiries.show', $enquiry) }}" class="hover:text-emerald-400">
                                        {{ $enquiry->name }}
                                    </a>
                                </div>
                                <div class="text-2xs text-slate-400 font-mono mt-0.5">
                                    <a href="tel:{{ $enquiry->phone }}" class="hover:underline">{{ $enquiry->phone }}</a>
                                    @if ($enquiry->email)
                                        <span class="text-slate-600">•</span>
                                        <span>{{ $enquiry->email }}</span>
                                    @endif
                                </div>
                            </td>

                            <!-- Source -->
                            <td class="py-3.5 px-4">
                                @php
                                    $sourceColors = [
                                        'website' => 'bg-sky-950 text-sky-400 border-sky-800',
                                        'whatsapp' => 'bg-emerald-950 text-emerald-400 border-emerald-800',
                                        'phone' => 'bg-amber-950 text-amber-400 border-amber-800',
                                        'referral' => 'bg-purple-950 text-purple-400 border-purple-800',
                                        'offline' => 'bg-slate-800 text-slate-300 border-slate-700',
                                    ];
                                @endphp
                                <span class="px-2 py-0.5 rounded-md text-3xs font-bold border uppercase tracking-wider {{ $sourceColors[$enquiry->source] ?? 'bg-slate-800 text-slate-300' }}">
                                    {{ \App\Models\Enquiry::SOURCES[$enquiry->source] ?? ucfirst($enquiry->source) }}
                                </span>
                            </td>

                            <!-- Property -->
                            <td class="py-3.5 px-4 max-w-xs">
                                @if ($enquiry->property)
                                    <a href="{{ route('admin.properties.show', $enquiry->property) }}" class="text-white hover:text-emerald-400 font-medium block truncate" :title="$enquiry->property->title">
                                        <span class="font-mono text-2xs text-emerald-400">[{{ $enquiry->property->reference }}]</span>
                                        {{ $enquiry->property->title }}
                                    </a>
                                    <span class="text-3xs text-slate-500 font-mono">{{ $enquiry->property->formatted_price }}</span>
                                @else
                                    <span class="text-slate-500 italic">General Agency Enquiry</span>
                                @endif
                            </td>

                            <!-- Message Preview -->
                            <td class="py-3.5 px-4 max-w-sm">
                                <p class="text-slate-300 truncate" title="{{ $enquiry->message }}">
                                    {{ $enquiry->message }}
                                </p>
                                @if ($enquiry->internal_notes)
                                    <span class="text-3xs text-amber-400/90 font-medium block truncate mt-0.5">
                                        Note: {{ $enquiry->internal_notes }}
                                    </span>
                                @endif
                            </td>

                            <!-- Status -->
                            <td class="py-3.5 px-4">
                                @php
                                    $statusPill = match ($enquiry->status) {
                                        'new' => 'bg-rose-950 text-rose-300 border-rose-800',
                                        'contacted' => 'bg-blue-950 text-blue-300 border-blue-800',
                                        'inspection_scheduled' => 'bg-amber-950 text-amber-300 border-amber-800',
                                        'negotiation' => 'bg-purple-950 text-purple-300 border-purple-800',
                                        'converted' => 'bg-emerald-950 text-emerald-300 border-emerald-800',
                                        'closed_lost' => 'bg-slate-800 text-slate-400 border-slate-700',
                                        default => 'bg-slate-800 text-slate-400 border-slate-700',
                                    };
                                @endphp
                                <span class="px-2.5 py-0.5 rounded-full text-3xs font-extrabold uppercase tracking-wider border {{ $statusPill }}">
                                    {{ \App\Models\Enquiry::STATUSES[$enquiry->status] ?? $enquiry->status }}
                                </span>
                            </td>

                            <!-- Date -->
                            <td class="py-3.5 px-4 text-2xs text-slate-400 whitespace-nowrap">
                                <div>{{ $enquiry->created_at->format('M j, Y') }}</div>
                                <div class="text-3xs text-slate-500 font-mono">{{ $enquiry->created_at->format('h:i A') }}</div>
                            </td>

                            <!-- Actions -->
                            <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.enquiries.show', $enquiry) }}"
                                       class="px-2.5 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-white font-semibold text-2xs transition-colors">
                                        View & Manage
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center">
                                <div class="w-12 h-12 rounded-2xl bg-slate-800 flex items-center justify-center text-slate-500 mx-auto mb-3">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                    </svg>
                                </div>
                                <p class="text-slate-300 font-semibold text-sm">No enquiries found</p>
                                <p class="text-slate-500 text-xs mt-1">There are currently no enquiries matching your filter criteria.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($enquiries->hasPages())
            <div class="p-4 border-t border-slate-800">
                {{ $enquiries->links() }}
            </div>
        @endif
    </div>

</div>
@endsection

