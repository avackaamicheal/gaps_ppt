@extends('layouts.admin')

@section('title', 'Prospective Buyers & Clients CRM')
@section('header', 'Customer Relationship Management')

@section('content')
<div class="space-y-6">

    <!-- Header Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-white tracking-tight">Prospective Buyers & Clients</h2>
            <p class="text-xs text-slate-400 mt-1">
                Centralized CRM records for all individuals who have made inquiries, requested inspections, or engaged with Victor Akande.
            </p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('admin.customers.create') }}"
               class="px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-lg shadow-emerald-950 transition-all flex items-center gap-2 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                </svg>
                Add Customer Profile
            </a>
        </div>
    </div>

    <!-- Search & Filter Bar -->
    <div class="p-4 rounded-2xl bg-slate-900 border border-slate-800">
        <form method="GET" action="{{ route('admin.customers.index') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3 items-center">
            <!-- Keyword Search -->
            <div class="sm:col-span-2">
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Search by client name, Nigerian phone number, email address..."
                       class="w-full px-4 py-2 rounded-xl bg-slate-950 border border-slate-700 text-xs text-white placeholder-slate-500 focus:outline-hidden focus:border-emerald-500">
            </div>

            <!-- Source Filter & Submit -->
            <div class="flex items-center gap-2">
                <select name="source" class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-xs text-white focus:outline-hidden focus:border-emerald-500">
                    <option value="">All Sources</option>
                    @foreach (\App\Models\Customer::SOURCES as $sourceCode => $sourceLabel)
                        <option value="{{ $sourceCode }}" {{ request('source') === $sourceCode ? 'selected' : '' }}>
                            {{ $sourceLabel }}
                        </option>
                    @endforeach
                </select>

                <button type="submit" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-white font-semibold text-xs transition-colors shrink-0 cursor-pointer">
                    Filter
                </button>

                @if (request()->hasAny(['search', 'source']))
                    <a href="{{ route('admin.customers.index') }}" class="text-xs text-slate-400 hover:text-rose-400 shrink-0">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    @if (session('status'))
        <div class="p-4 rounded-2xl bg-emerald-950/80 border border-emerald-800 text-emerald-300 text-xs flex items-center gap-2">
            <svg class="w-4 h-4 shrink-0 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            <span>{{ session('status') }}</span>
        </div>
    @endif

    <!-- Customers Data Table -->
    <div class="rounded-2xl bg-slate-900 border border-slate-800 overflow-hidden shadow-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="border-b border-slate-800 bg-slate-950/60 text-2xs uppercase tracking-wider text-slate-400 font-bold">
                        <th class="py-3 px-4">Client Name</th>
                        <th class="py-3 px-4">Phone Number</th>
                        <th class="py-3 px-4">Email</th>
                        <th class="py-3 px-4">Origin Source</th>
                        <th class="py-3 px-4">Inquiries</th>
                        <th class="py-3 px-4">Inspections</th>
                        <th class="py-3 px-4">Added On</th>
                        <th class="py-3 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800 text-slate-300">
                    @forelse ($customers as $customer)
                        <tr class="hover:bg-slate-800/40 transition-colors">
                            <!-- Name -->
                            <td class="py-3.5 px-4 font-bold text-white">
                                <a href="{{ route('admin.customers.show', $customer) }}" class="hover:text-emerald-400 flex items-center gap-2">
                                    <span class="w-7 h-7 rounded-lg bg-emerald-950 border border-emerald-800 text-emerald-300 font-bold flex items-center justify-center text-3xs shrink-0">
                                        {{ strtoupper(substr($customer->name, 0, 2)) }}
                                    </span>
                                    <span>{{ $customer->name }}</span>
                                </a>
                            </td>

                            <!-- Phone -->
                            <td class="py-3.5 px-4 font-mono text-2xs">
                                <a href="tel:{{ $customer->phone }}" class="hover:text-emerald-400">{{ $customer->phone }}</a>
                            </td>

                            <!-- Email -->
                            <td class="py-3.5 px-4 text-2xs text-slate-400">
                                {{ $customer->email ?: '—' }}
                            </td>

                            <!-- Source -->
                            <td class="py-3.5 px-4">
                                <span class="px-2 py-0.5 rounded text-3xs font-bold uppercase tracking-wider bg-slate-800 text-slate-300 border border-slate-700">
                                    {{ \App\Models\Customer::SOURCES[$customer->source] ?? ucfirst($customer->source) }}
                                </span>
                            </td>

                            <!-- Inquiries Count -->
                            <td class="py-3.5 px-4 font-mono text-xs">
                                <span class="px-2 py-0.5 rounded-full {{ $customer->enquiries_count > 0 ? 'bg-sky-950 text-sky-400 border border-sky-800' : 'text-slate-500' }}">
                                    {{ $customer->enquiries_count }}
                                </span>
                            </td>

                            <!-- Inspections Count -->
                            <td class="py-3.5 px-4 font-mono text-xs">
                                <span class="px-2 py-0.5 rounded-full {{ $customer->inspections_count > 0 ? 'bg-amber-950 text-amber-400 border border-amber-800' : 'text-slate-500' }}">
                                    {{ $customer->inspections_count }}
                                </span>
                            </td>

                            <!-- Date Added -->
                            <td class="py-3.5 px-4 text-2xs text-slate-400 whitespace-nowrap">
                                {{ $customer->created_at->format('M j, Y') }}
                            </td>

                            <!-- Actions -->
                            <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.customers.show', $customer) }}"
                                       class="px-2.5 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-white font-semibold text-2xs transition-colors">
                                        Profile 360°
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center">
                                <p class="text-slate-300 font-semibold text-sm">No customer records found</p>
                                <p class="text-slate-500 text-xs mt-1">Customers will be automatically populated as enquiries arrive, or you can create one manually.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($customers->hasPages())
            <div class="p-4 border-t border-slate-800">
                {{ $customers->links() }}
            </div>
        @endif
    </div>

</div>
@endsection

