@extends('layouts.admin')

@section('title', 'Manage Properties')
@section('header', 'Property Listings & Inventory')

@section('content')
<div class="space-y-6">

    <!-- Action Bar & Summary -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-white tracking-tight">Property Inventory</h2>
            <p class="text-xs text-slate-400 mt-1">
                Manage your land and building listings, publication statuses, images, and confidential documents.
            </p>
        </div>

        <a href="{{ route('admin.properties.create') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-semibold text-xs shadow-md shadow-emerald-950 transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Add New Property
        </a>
    </div>

    <!-- Status Filters Bar -->
    <div class="flex flex-wrap items-center gap-2 text-xs">
        <a href="{{ route('admin.properties.index', request()->except('status', 'page')) }}"
           class="px-3 py-1.5 rounded-lg border font-medium transition-colors {{ !request()->filled('status') ? 'bg-emerald-600 text-white border-emerald-500' : 'bg-slate-900 text-slate-400 border-slate-800 hover:text-white' }}">
            All ({{ array_sum($statusCounts) }})
        </a>
        @foreach (\App\Models\Property::STATUSES as $statusCode => $statusLabel)
            @php $count = $statusCounts[$statusCode] ?? 0; @endphp
            <a href="{{ route('admin.properties.index', array_merge(request()->except('status', 'page'), ['status' => $statusCode])) }}"
               class="px-3 py-1.5 rounded-lg border font-medium transition-colors {{ request('status') === $statusCode ? 'bg-emerald-600 text-white border-emerald-500' : 'bg-slate-900 text-slate-400 border-slate-800 hover:text-white' }}">
                {{ $statusLabel }} ({{ $count }})
            </a>
        @endforeach
    </div>

    <!-- Search & Filter Controls -->
    <div class="p-4 rounded-2xl bg-slate-900 border border-slate-800">
        <form method="GET" action="{{ route('admin.properties.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3">
            @if (request('status'))
                <input type="hidden" name="status" value="{{ request('status') }}">
            @endif

            <div class="sm:col-span-5">
                <input type="text" name="keyword" value="{{ request('keyword') }}"
                       placeholder="Search title, reference (LAND-0001), location..."
                       class="w-full px-3.5 py-2 rounded-xl bg-slate-950 border border-slate-700 text-xs text-white placeholder-slate-500 focus:outline-hidden focus:border-emerald-500">
            </div>

            <div class="sm:col-span-4">
                <select name="category" class="w-full px-3.5 py-2 rounded-xl bg-slate-950 border border-slate-700 text-xs text-slate-300 focus:outline-hidden focus:border-emerald-500">
                    <option value="">All Categories</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="sm:col-span-3 flex items-center gap-2">
                <button type="submit" class="flex-1 py-2 px-3 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 font-semibold text-xs border border-slate-700 transition-colors">
                    Filter
                </button>
                @if (request()->hasAny(['keyword', 'category', 'status']))
                    <a href="{{ route('admin.properties.index') }}" class="py-2 px-3 rounded-xl bg-slate-950 hover:bg-slate-800 text-slate-400 text-xs border border-slate-800 transition-colors">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Properties Table -->
    <div class="rounded-2xl bg-slate-900 border border-slate-800 overflow-hidden shadow-xl">
        @if ($properties->isEmpty())
            <div class="p-12 text-center">
                <div class="w-12 h-12 rounded-2xl bg-slate-800 flex items-center justify-center text-slate-500 mx-auto mb-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
                <h3 class="text-sm font-bold text-white">No Properties Found</h3>
                <p class="text-xs text-slate-400 mt-1 max-w-sm mx-auto">
                    @if (request()->hasAny(['keyword', 'category', 'status']))
                        No listings match your filter criteria. Try adjusting your search or reset filters.
                    @else
                        You have not created any property listings yet. Click below to add your first land or building.
                    @endif
                </p>
                @if (!request()->hasAny(['keyword', 'category', 'status']))
                    <div class="mt-4">
                        <a href="{{ route('admin.properties.create') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-semibold text-xs shadow-md">
                            Add First Property
                        </a>
                    </div>
                @endif
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-950/60 border-b border-slate-800 text-slate-400 uppercase tracking-wider font-semibold">
                        <tr>
                            <th class="py-3 px-4">Property</th>
                            <th class="py-3 px-4">Category</th>
                            <th class="py-3 px-4">Location</th>
                            <th class="py-3 px-4">Price</th>
                            <th class="py-3 px-4">Status</th>
                            <th class="py-3 px-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/80 text-slate-300">
                        @foreach ($properties as $property)
                            <tr class="hover:bg-slate-800/30 transition-colors">
                                <!-- Property Thumbnail & Title -->
                                <td class="py-3 px-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-14 h-14 rounded-xl bg-slate-800 shrink-0 overflow-hidden border border-slate-700/60 flex items-center justify-center">
                                            @if ($property->cover_url)
                                                <img src="{{ $property->cover_url }}" alt="{{ $property->title }}" class="w-full h-full object-cover">
                                            @else
                                                <svg class="w-6 h-6 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                            @endif
                                        </div>
                                        <div class="min-w-0">
                                            <div class="flex items-center gap-2">
                                                <span class="font-mono text-2xs font-bold text-emerald-400 bg-emerald-950/80 px-1.5 py-0.5 rounded border border-emerald-800/60">
                                                    {{ $property->reference }}
                                                </span>
                                                @if ($property->is_featured)
                                                    <span class="text-2xs font-bold text-amber-400 bg-amber-950/60 px-1.5 py-0.5 rounded border border-amber-800/60">
                                                        ★ Featured
                                                    </span>
                                                @endif
                                            </div>
                                            <a href="{{ route('admin.properties.show', $property) }}" class="block font-bold text-white hover:text-emerald-400 truncate mt-1">
                                                {{ $property->title }}
                                            </a>
                                            <p class="text-2xs text-slate-500 mt-0.5">
                                                {{ $property->property_type ?: $property->category->name }} • {{ $property->images->count() }} images • {{ $property->documents->count() }} docs
                                            </p>
                                        </div>
                                    </div>
                                </td>

                                <!-- Category -->
                                <td class="py-3 px-4">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-2xs font-semibold {{ $property->isLand() ? 'bg-teal-950 text-teal-300 border border-teal-800/60' : 'bg-sky-950 text-sky-300 border border-sky-800/60' }}">
                                        {{ $property->category->name }}
                                    </span>
                                </td>

                                <!-- Location -->
                                <td class="py-3 px-4">
                                    <p class="font-medium text-slate-200">{{ $property->neighbourhood }}</p>
                                    <p class="text-2xs text-slate-400">{{ $property->lga }}, {{ $property->state }}</p>
                                </td>

                                <!-- Price -->
                                <td class="py-3 px-4 font-bold text-emerald-400 font-mono text-xs">
                                    {{ $property->formatted_price }}
                                </td>

                                <!-- Status & Quick Update -->
                                <td class="py-3 px-4">
                                    <form method="POST" action="{{ route('admin.properties.status', $property) }}">
                                        @csrf
                                        @method('PATCH')
                                        <select name="status" onchange="this.form.submit()"
                                                class="px-2 py-1 rounded-md text-2xs font-semibold cursor-pointer border focus:outline-hidden
                                                @if($property->status === 'published') bg-emerald-950 text-emerald-300 border-emerald-700
                                                @elseif($property->status === 'draft') bg-slate-800 text-slate-300 border-slate-700
                                                @elseif($property->status === 'sold') bg-rose-950 text-rose-300 border-rose-800
                                                @elseif($property->status === 'under_negotiation') bg-amber-950 text-amber-300 border-amber-700
                                                @elseif($property->status === 'reserved') bg-purple-950 text-purple-300 border-purple-800
                                                @else bg-slate-900 text-slate-400 border-slate-800 @endif">
                                            @foreach (\App\Models\Property::STATUSES as $code => $label)
                                                <option value="{{ $code }}" {{ $property->status === $code ? 'selected' : '' }}>
                                                    {{ $label }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </form>
                                </td>

                                <!-- Actions -->
                                <td class="py-3 px-4 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        @if ($property->status === 'published')
                                            <a href="{{ route('properties.show', $property->slug) }}" target="_blank" title="View Public Page"
                                               class="p-1.5 rounded-lg text-slate-400 hover:text-emerald-400 hover:bg-slate-800 transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                                </svg>
                                            </a>
                                        @endif

                                        <a href="{{ route('admin.properties.show', $property) }}" title="Inspect Property"
                                           class="p-1.5 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </a>

                                        <a href="{{ route('admin.properties.edit', $property) }}" title="Edit Property"
                                           class="p-1.5 rounded-lg text-slate-400 hover:text-emerald-400 hover:bg-slate-800 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </a>

                                        <form method="POST" action="{{ route('admin.properties.destroy', $property) }}"
                                              onsubmit="return confirm('Are you sure you want to delete property {{ $property->reference }}? All images and documents will also be removed.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" title="Delete Property" class="p-1.5 rounded-lg text-slate-400 hover:text-rose-400 hover:bg-rose-950/30 transition-colors cursor-pointer">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if ($properties->hasPages())
                <div class="p-4 border-t border-slate-800">
                    {{ $properties->links() }}
                </div>
            @endif
        @endif
    </div>

</div>
@endsection
