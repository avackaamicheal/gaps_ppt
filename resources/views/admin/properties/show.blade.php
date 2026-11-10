@extends('layouts.admin')

@section('title', $property->reference . ' — ' . $property->title)
@section('header', 'Property Details: ' . $property->reference)

@section('content')
<div class="max-w-5xl mx-auto space-y-6">

    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <a href="{{ route('admin.properties.index') }}" class="text-xs text-slate-400 hover:text-emerald-400 flex items-center gap-1 mb-2">
                &larr; Back to Listings
            </a>
            <div class="flex flex-wrap items-center gap-3">
                <h2 class="text-2xl font-bold text-white tracking-tight">{{ $property->title }}</h2>
                <span class="font-mono text-xs font-bold text-emerald-400 bg-emerald-950 px-2.5 py-1 rounded-lg border border-emerald-800">
                    {{ $property->reference }}
                </span>
            </div>
            <p class="text-xs text-slate-400 mt-1">
                {{ $property->display_location }} • Category: {{ $property->category->name }}
            </p>
        </div>

        <div class="flex items-center gap-2">
            @if ($property->status === 'published')
                <a href="{{ route('properties.show', $property->slug) }}" target="_blank"
                   class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-semibold border border-slate-700 flex items-center gap-1.5 transition-colors">
                    <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                    </svg>
                    Public Page
                </a>
            @endif

            <a href="{{ route('admin.properties.edit', $property) }}"
               class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-semibold shadow-md transition-colors flex items-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                Edit
            </a>
        </div>
    </div>

    <!-- Quick Status & Price Card -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="p-5 rounded-2xl bg-slate-900 border border-slate-800">
            <span class="text-2xs uppercase tracking-wider text-slate-400">Asking Price</span>
            <p class="text-2xl font-extrabold text-emerald-400 font-mono mt-1">{{ $property->formatted_price }}</p>
        </div>

        <div class="p-5 rounded-2xl bg-slate-900 border border-slate-800">
            <span class="text-2xs uppercase tracking-wider text-slate-400">Current Status</span>
            <div class="mt-1 flex items-center gap-2">
                <span class="px-2.5 py-1 rounded-md text-xs font-bold uppercase tracking-wider
                    @if($property->status === 'published') bg-emerald-950 text-emerald-300 border border-emerald-800
                    @elseif($property->status === 'draft') bg-slate-800 text-slate-300 border border-slate-700
                    @elseif($property->status === 'sold') bg-rose-950 text-rose-300 border border-rose-800
                    @else bg-amber-950 text-amber-300 border border-amber-800 @endif">
                    {{ \App\Models\Property::STATUSES[$property->status] ?? $property->status }}
                </span>
                @if ($property->is_featured)
                    <span class="px-2 py-0.5 rounded text-2xs font-bold bg-amber-950 text-amber-300 border border-amber-800">
                        ★ Featured
                    </span>
                @endif
            </div>
        </div>

        <div class="p-5 rounded-2xl bg-slate-900 border border-slate-800">
            <span class="text-2xs uppercase tracking-wider text-slate-400">Inventory Classification</span>
            <p class="text-sm font-bold text-white mt-1">{{ $property->property_type ?: $property->category->name }}</p>
        </div>
    </div>

    <!-- Images Gallery -->
    <div class="p-6 rounded-2xl bg-slate-900 border border-slate-800 space-y-4">
        <h3 class="text-sm font-bold text-white uppercase tracking-wider pb-3 border-b border-slate-800">
            Property Photography ({{ $property->images->count() }})
        </h3>

        @if ($property->images->isEmpty())
            <p class="text-xs text-slate-500 py-4">No photos uploaded for this property.</p>
        @else
            <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-6 gap-3">
                @foreach ($property->images as $img)
                    <div class="relative rounded-xl overflow-hidden border {{ $img->is_cover ? 'border-emerald-500 ring-2 ring-emerald-500/50' : 'border-slate-800' }} bg-slate-950">
                        <img src="{{ $img->url }}" class="w-full h-24 object-cover">
                        @if ($img->is_cover)
                            <span class="absolute top-1 left-1 px-1.5 py-0.5 rounded text-3xs font-extrabold bg-emerald-600 text-white">
                                COVER
                            </span>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Specifications Grid -->
    <div class="p-6 rounded-2xl bg-slate-900 border border-slate-800 space-y-4">
        <h3 class="text-sm font-bold text-white uppercase tracking-wider pb-3 border-b border-slate-800">
            Specifications & Features
        </h3>

        @if ($property->isLand())
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 text-xs">
                <div>
                    <span class="text-2xs uppercase text-slate-400">Plot Size:</span>
                    <p class="font-bold text-white mt-0.5">{{ $property->plot_size ?: '—' }}</p>
                </div>
                <div>
                    <span class="text-2xs uppercase text-slate-400">Dimensions:</span>
                    <p class="font-bold text-white mt-0.5">{{ $property->plot_dimensions ?: '—' }}</p>
                </div>
                <div>
                    <span class="text-2xs uppercase text-slate-400">Number of Plots:</span>
                    <p class="font-bold text-white mt-0.5">{{ $property->number_of_plots ?: '—' }}</p>
                </div>
                <div>
                    <span class="text-2xs uppercase text-slate-400">Title Documentation:</span>
                    <p class="font-bold text-emerald-400 mt-0.5">{{ $property->title_type ?: '—' }}</p>
                </div>
                <div>
                    <span class="text-2xs uppercase text-slate-400">Permitted Use:</span>
                    <p class="font-bold text-white mt-0.5">{{ $property->land_use ?: '—' }}</p>
                </div>
                <div>
                    <span class="text-2xs uppercase text-slate-400">Topography:</span>
                    <p class="font-bold text-white mt-0.5">{{ $property->topography ?: '—' }}</p>
                </div>
                <div>
                    <span class="text-2xs uppercase text-slate-400">Registered Survey:</span>
                    <p class="font-bold {{ $property->has_survey ? 'text-emerald-400' : 'text-slate-400' }} mt-0.5">
                        {{ $property->has_survey ? 'Yes (Verified)' : 'No' }}
                    </p>
                </div>
            </div>
        @else
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-xs mb-4">
                <div>
                    <span class="text-2xs uppercase text-slate-400">Bedrooms:</span>
                    <p class="font-bold text-white mt-0.5">{{ $property->bedrooms ?? '—' }}</p>
                </div>
                <div>
                    <span class="text-2xs uppercase text-slate-400">Bathrooms:</span>
                    <p class="font-bold text-white mt-0.5">{{ $property->bathrooms ?? '—' }}</p>
                </div>
                <div>
                    <span class="text-2xs uppercase text-slate-400">Toilets:</span>
                    <p class="font-bold text-white mt-0.5">{{ $property->toilets ?? '—' }}</p>
                </div>
                <div>
                    <span class="text-2xs uppercase text-slate-400">Parking Spaces:</span>
                    <p class="font-bold text-white mt-0.5">{{ $property->parking_spaces ?? '—' }}</p>
                </div>
                <div>
                    <span class="text-2xs uppercase text-slate-400">Floors:</span>
                    <p class="font-bold text-white mt-0.5">{{ $property->floors ?? '—' }}</p>
                </div>
                <div>
                    <span class="text-2xs uppercase text-slate-400">Building Condition:</span>
                    <p class="font-bold text-white mt-0.5">{{ $property->building_condition ?: '—' }}</p>
                </div>
            </div>

            @if (!empty($property->features))
                <div class="pt-3 border-t border-slate-800">
                    <span class="text-2xs uppercase text-slate-400 block mb-2">Amenities & Features:</span>
                    <div class="flex flex-wrap gap-2">
                        @foreach ($property->features as $feature)
                            <span class="px-2.5 py-1 rounded-lg bg-slate-950 border border-slate-800 text-xs text-slate-300">
                                ✓ {{ $feature }}
                            </span>
                        @endforeach
                    </div>
                </div>
            @endif
        @endif
    </div>

    <!-- Description -->
    <div class="p-6 rounded-2xl bg-slate-900 border border-slate-800 space-y-3">
        <h3 class="text-sm font-bold text-white uppercase tracking-wider pb-3 border-b border-slate-800">
            Description
        </h3>
        <div class="text-xs text-slate-300 leading-relaxed whitespace-pre-line">
            {{ $property->description }}
        </div>
    </div>

    <!-- Documents -->
    <div class="p-6 rounded-2xl bg-slate-900 border border-slate-800 space-y-4">
        <h3 class="text-sm font-bold text-white uppercase tracking-wider pb-3 border-b border-slate-800">
            Attached Documents ({{ $property->documents->count() }})
        </h3>

        @if ($property->documents->isEmpty())
            <p class="text-xs text-slate-500 py-3">No documents attached.</p>
        @else
            <div class="space-y-2">
                @foreach ($property->documents as $doc)
                    <div class="flex items-center justify-between p-3 rounded-xl bg-slate-950 border border-slate-800 text-xs">
                        <div>
                            <p class="font-bold text-white">{{ $doc->title }}</p>
                            <p class="text-2xs text-slate-400">
                                {{ $doc->document_type }} • {{ $doc->formatted_size }} •
                                @if ($doc->is_public)
                                    <span class="text-emerald-400 font-semibold">Publicly Accessible</span>
                                @else
                                    <span class="text-amber-400 font-semibold">Confidential (Private)</span>
                                @endif
                            </p>
                        </div>
                        <a href="{{ route('admin.properties.documents.download', [$property, $doc]) }}"
                           class="px-3 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-white text-2xs font-semibold">
                            Download
                        </a>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

</div>
@endsection
