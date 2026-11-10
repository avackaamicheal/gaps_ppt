@extends('layouts.public')

@section('title', 'Verified Land & Properties For Sale — Victor Akande')

@section('content')
<!-- Header Banner -->
<section class="bg-slate-900 text-white py-14 border-b border-slate-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-xs font-semibold uppercase tracking-wider mb-3">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                Verified Nigerian Listings
            </span>
            <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight">Available Property Inventory</h1>
            <p class="text-sm sm:text-base text-slate-300 mt-2 leading-relaxed">
                Discover pre-screened land plots, modern residential developments, and commercial properties with verified title documentation.
            </p>
        </div>
    </div>
</section>

<!-- Filter Section -->
<section class="bg-white border-b border-slate-200 py-6 sticky top-20 z-20 shadow-xs"
         x-data="{
             locationConfig: {{ Js::from($locationConfig) }},
             selectedState: '{{ request('state', '') }}',
             selectedLga: '{{ request('lga', '') }}',
             get lgas() {
                 return (this.selectedState && this.locationConfig.states && this.locationConfig.states[this.selectedState])
                     ? Object.keys(this.locationConfig.states[this.selectedState])
                     : [];
             }
         }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <form method="GET" action="{{ route('properties.index') }}" class="space-y-4">

            <!-- Category Filter Tabs -->
            <div class="flex flex-wrap items-center gap-2 pb-2 border-b border-slate-100">
                <a href="{{ route('properties.index', request()->except('category', 'page')) }}"
                   class="px-4 py-2 rounded-xl text-xs font-bold transition-all {{ !request()->filled('category') ? 'bg-emerald-600 text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                    All Properties
                </a>
                @foreach ($categories as $cat)
                    <a href="{{ route('properties.index', array_merge(request()->except('category', 'page'), ['category' => $cat->slug])) }}"
                       class="px-4 py-2 rounded-xl text-xs font-bold transition-all {{ request('category') === $cat->slug ? 'bg-emerald-600 text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                        {{ $cat->name }}
                    </a>
                @endforeach
            </div>

            <!-- Detailed Search Controls -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
                @if (request('category'))
                    <input type="hidden" name="category" value="{{ request('category') }}">
                @endif

                <!-- Keyword -->
                <div>
                    <input type="text" name="keyword" value="{{ request('keyword') }}"
                           placeholder="Search title, neighbourhood, reference..."
                           class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-300 text-xs text-slate-800 placeholder-slate-400 focus:outline-hidden focus:ring-2 focus:ring-emerald-500 focus:bg-white">
                </div>

                <!-- State -->
                <div>
                    <select name="state" x-model="selectedState"
                            class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-300 text-xs text-slate-700 focus:outline-hidden focus:ring-2 focus:ring-emerald-500 focus:bg-white">
                        <option value="">All States</option>
                        @foreach (array_keys(config('nigerian_locations.states', [])) as $st)
                            <option value="{{ $st }}" {{ request('state') === $st ? 'selected' : '' }}>
                                {{ $st }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- LGA -->
                <div>
                    <select name="lga" x-model="selectedLga"
                            class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-300 text-xs text-slate-700 focus:outline-hidden focus:ring-2 focus:ring-emerald-500 focus:bg-white">
                        <option value="">All LGAs</option>
                        <template x-for="lga in lgas" :key="lga">
                            <option :value="lga" x-text="lga" :selected="lga === '{{ request('lga') }}'"></option>
                        </template>
                    </select>
                </div>

                <!-- Max Price -->
                <div>
                    <select name="max_price"
                            class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-300 text-xs text-slate-700 focus:outline-hidden focus:ring-2 focus:ring-emerald-500 focus:bg-white">
                        <option value="">Max Budget (Any)</option>
                        <option value="25000000" {{ request('max_price') == '25000000' ? 'selected' : '' }}>Up to ₦25 Million</option>
                        <option value="50000000" {{ request('max_price') == '50000000' ? 'selected' : '' }}>Up to ₦50 Million</option>
                        <option value="100000000" {{ request('max_price') == '100000000' ? 'selected' : '' }}>Up to ₦100 Million</option>
                        <option value="250000000" {{ request('max_price') == '250000000' ? 'selected' : '' }}>Up to ₦250 Million</option>
                        <option value="500000000" {{ request('max_price') == '500000000' ? 'selected' : '' }}>Up to ₦500 Million</option>
                    </select>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center gap-2">
                    <button type="submit" class="flex-1 py-2.5 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-sm transition-all cursor-pointer">
                        Filter Results
                    </button>
                    @if (request()->hasAny(['keyword', 'state', 'lga', 'category', 'max_price', 'min_price']))
                        <a href="{{ route('properties.index') }}" class="py-2.5 px-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-semibold transition-colors">
                            Reset
                        </a>
                    @endif
                </div>
            </div>

        </form>
    </div>
</section>

<!-- Properties Grid -->
<section class="py-12 bg-slate-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="flex items-center justify-between mb-8">
            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">
                Showing {{ $properties->total() }} Available {{ Str::plural('Property', $properties->total()) }}
            </p>
        </div>

        @if ($properties->isEmpty())
            <div class="py-16 text-center bg-white rounded-3xl border border-slate-200 p-8 shadow-xs">
                <div class="w-14 h-14 rounded-2xl bg-slate-100 flex items-center justify-center text-slate-400 mx-auto mb-4">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-slate-900">No Properties Found</h3>
                <p class="text-xs text-slate-500 mt-1.5 max-w-sm mx-auto">
                    We could not find published listings matching your specified filters. Try selecting a different state or reset your filters.
                </p>
                <div class="mt-5">
                    <a href="{{ route('properties.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs shadow-sm">
                        View All Available Properties
                    </a>
                </div>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach ($properties as $property)
                    <article class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-xs hover:shadow-xl transition-all flex flex-col group">

                        <!-- Thumbnail & Cover -->
                        <a href="{{ route('properties.show', $property->slug) }}" class="relative block h-56 bg-slate-900 overflow-hidden">
                            @if ($property->cover_url)
                                <img src="{{ $property->cover_url }}" alt="{{ $property->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-gradient-to-tr from-slate-800 to-slate-700 text-slate-400">
                                    <svg class="w-10 h-10 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                            @endif

                            <!-- Top Badges -->
                            <div class="absolute top-3 left-3 flex flex-wrap gap-1.5">
                                <span class="px-2.5 py-1 rounded-md text-2xs font-extrabold uppercase tracking-wider text-white shadow-sm {{ $property->isLand() ? 'bg-teal-700' : 'bg-emerald-700' }}">
                                    {{ $property->category->name }}
                                </span>
                                @if ($property->is_featured)
                                    <span class="px-2 py-1 rounded-md text-2xs font-extrabold bg-amber-500 text-slate-950 shadow-sm">
                                        ★ Featured
                                    </span>
                                @endif
                            </div>

                            <span class="absolute bottom-3 right-3 px-2 py-0.5 rounded text-3xs font-mono font-bold bg-slate-950/80 text-slate-300">
                                {{ $property->reference }}
                            </span>
                        </a>

                        <!-- Card Body -->
                        <div class="p-6 flex-1 flex flex-col justify-between">
                            <div>
                                <div class="flex items-center gap-1.5 text-xs text-slate-500 mb-1.5">
                                    <svg class="w-3.5 h-3.5 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                                    </svg>
                                    <span class="truncate">{{ $property->neighbourhood }}, {{ $property->state }}</span>
                                </div>

                                <h2 class="text-base font-bold text-slate-900 group-hover:text-emerald-600 transition-colors line-clamp-2">
                                    <a href="{{ route('properties.show', $property->slug) }}">
                                        {{ $property->title }}
                                    </a>
                                </h2>

                                <p class="text-xs text-slate-500 mt-2 line-clamp-2">
                                    {{ $property->description }}
                                </p>
                            </div>

                            <!-- Specific Attributes Snippet -->
                            <div class="mt-4 pt-4 border-t border-slate-100">
                                @if ($property->isLand())
                                    <div class="flex items-center justify-between text-2xs text-slate-600">
                                        <span>Size: <strong class="text-slate-900">{{ $property->plot_size ?: ($property->number_of_plots ? $property->number_of_plots . ' Plots' : 'Verified Plot') }}</strong></span>
                                        <span class="truncate max-w-[140px] text-emerald-700 font-semibold">{{ $property->title_type ?: 'Surveyed' }}</span>
                                    </div>
                                @else
                                    <div class="flex items-center gap-4 text-2xs text-slate-600">
                                        @if ($property->bedrooms)
                                            <span class="flex items-center gap-1">
                                                <strong>{{ $property->bedrooms }}</strong> Beds
                                            </span>
                                        @endif
                                        @if ($property->bathrooms)
                                            <span class="flex items-center gap-1">
                                                <strong>{{ $property->bathrooms }}</strong> Baths
                                            </span>
                                        @endif
                                        @if ($property->building_condition)
                                            <span class="text-slate-500 truncate ml-auto">
                                                {{ $property->building_condition }}
                                            </span>
                                        @endif
                                    </div>
                                @endif

                                <div class="mt-4 flex items-center justify-between">
                                    <div>
                                        <span class="block text-3xs uppercase font-semibold text-slate-400">Asking Price</span>
                                        <span class="text-lg font-black text-slate-900 font-mono tracking-tight">
                                            {{ $property->formatted_price }}
                                        </span>
                                    </div>

                                    <a href="{{ route('properties.show', $property->slug) }}"
                                       class="px-3.5 py-2 rounded-xl bg-slate-900 group-hover:bg-emerald-600 text-white text-xs font-bold transition-colors">
                                        View Details &rarr;
                                    </a>
                                </div>
                            </div>
                        </div>

                    </article>
                @endforeach
            </div>

            <!-- Pagination -->
            @if ($properties->hasPages())
                <div class="mt-12">
                    {{ $properties->links() }}
                </div>
            @endif
        @endif

    </div>
</section>
@endsection
