@extends('layouts.public')

@section('title', $property->title . ' (' . $property->reference . ')')

@section('content')
<div class="bg-slate-50 py-10"
     x-data="{
         images: {{ Js::from($property->images->map(fn($img) => ['id' => $img->id, 'url' => $img->url])) }},
         currentIndex: 0,
         get currentImageUrl() {
             return this.images.length > 0 ? this.images[this.currentIndex].url : null;
         },
         prevImage() {
             this.currentIndex = (this.currentIndex > 0) ? this.currentIndex - 1 : this.images.length - 1;
         },
         nextImage() {
             this.currentIndex = (this.currentIndex < this.images.length - 1) ? this.currentIndex + 1 : 0;
         }
     }">

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Breadcrumbs -->
        <nav class="flex items-center gap-2 text-xs text-slate-500 mb-6">
            <a href="{{ route('home') }}" class="hover:text-emerald-600 transition-colors">Home</a>
            <span>/</span>
            <a href="{{ route('properties.index') }}" class="hover:text-emerald-600 transition-colors">Properties</a>
            <span>/</span>
            <a href="{{ route('properties.index', ['category' => $property->category->slug]) }}" class="hover:text-emerald-600 transition-colors">
                {{ $property->category->name }}
            </a>
            <span>/</span>
            <span class="text-slate-800 font-semibold truncate max-w-xs">{{ $property->reference }}</span>
        </nav>

        <!-- Main Property Grid: 8 Cols Content / 4 Cols Sticky Action Card -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

            <!-- Left: Gallery & Property Overview (8 Cols) -->
            <div class="lg:col-span-8 space-y-8">

                <!-- Header Block -->
                <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-xs">
                    <div class="flex flex-wrap items-center gap-2 mb-3">
                        <span class="font-mono text-xs font-bold text-emerald-800 bg-emerald-100 px-2.5 py-1 rounded-md">
                            REF: {{ $property->reference }}
                        </span>
                        <span class="text-xs font-bold text-white px-2.5 py-1 rounded-md {{ $property->isLand() ? 'bg-teal-700' : 'bg-emerald-700' }}">
                            {{ $property->category->name }}
                        </span>
                        @if ($property->property_type)
                            <span class="text-xs font-semibold text-slate-700 bg-slate-100 px-2.5 py-1 rounded-md">
                                {{ $property->property_type }}
                            </span>
                        @endif
                    </div>

                    <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight leading-tight">
                        {{ $property->title }}
                    </h1>

                    <div class="flex items-center gap-2 text-sm text-slate-600 mt-2.5">
                        <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                        </svg>
                        <span>{{ $property->display_location }}</span>
                        @if ($property->street_address)
                            <span class="text-slate-400">• {{ $property->street_address }}</span>
                        @endif
                    </div>
                </div>

                <!-- Image Gallery -->
                <div class="bg-white rounded-3xl p-4 sm:p-6 border border-slate-200 shadow-xs space-y-4 overflow-hidden">
                    <!-- Main Stage -->
                    <div class="relative h-80 sm:h-[460px] bg-slate-900 rounded-2xl overflow-hidden flex items-center justify-center">
                        <template x-if="currentImageUrl">
                            <img :src="currentImageUrl" alt="{{ $property->title }}" class="w-full h-full object-cover">
                        </template>
                        <template x-if="!currentImageUrl">
                            <div class="text-center text-slate-400">
                                <svg class="w-12 h-12 mx-auto mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <span class="text-xs">No Photos Available</span>
                            </div>
                        </template>

                        <!-- Next / Prev Controls -->
                        <div x-show="images.length > 1" class="absolute inset-x-4 flex items-center justify-between pointer-events-none">
                            <button type="button" @click="prevImage()" class="p-2.5 rounded-full bg-slate-950/70 hover:bg-slate-950 text-white shadow-lg pointer-events-auto transition-transform hover:scale-105">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                </svg>
                            </button>
                            <button type="button" @click="nextImage()" class="p-2.5 rounded-full bg-slate-950/70 hover:bg-slate-950 text-white shadow-lg pointer-events-auto transition-transform hover:scale-105">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </button>
                        </div>

                        <!-- Image Counter -->
                        <div x-show="images.length > 0" class="absolute bottom-3 right-3 px-3 py-1 rounded-lg bg-slate-950/80 text-white text-xs font-mono">
                            <span x-text="currentIndex + 1"></span> / <span x-text="images.length"></span>
                        </div>
                    </div>

                    <!-- Thumbnails Strip -->
                    <div x-show="images.length > 1" class="flex items-center gap-3 overflow-x-auto pb-2">
                        <template x-for="(img, idx) in images" :key="img.id">
                            <button type="button" @click="currentIndex = idx"
                                    :class="currentIndex === idx ? 'ring-2 ring-emerald-600 border-transparent' : 'opacity-70 hover:opacity-100 border-slate-200'"
                                    class="w-20 h-16 rounded-xl overflow-hidden shrink-0 border transition-all cursor-pointer">
                                <img :src="img.url" class="w-full h-full object-cover">
                            </button>
                        </template>
                    </div>
                </div>

                <!-- Specifications & Verified Details -->
                <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-xs space-y-6">
                    <h2 class="text-lg font-bold text-slate-900 border-b border-slate-100 pb-3">
                        Key Specifications & Attributes
                    </h2>

                    @if ($property->isLand())
                        <!-- Land Specs -->
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-6 text-sm">
                            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-100">
                                <span class="text-xs text-slate-500 font-semibold block uppercase tracking-wider">Plot Size</span>
                                <span class="text-base font-bold text-slate-900 mt-1 block">{{ $property->plot_size ?: '—' }}</span>
                            </div>

                            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-100">
                                <span class="text-xs text-slate-500 font-semibold block uppercase tracking-wider">Title Type</span>
                                <span class="text-sm font-bold text-emerald-700 mt-1 block">{{ $property->title_type ?: 'Registered Title' }}</span>
                            </div>

                            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-100">
                                <span class="text-xs text-slate-500 font-semibold block uppercase tracking-wider">Dimensions</span>
                                <span class="text-base font-bold text-slate-900 mt-1 block">{{ $property->plot_dimensions ?: 'Standard Plot' }}</span>
                            </div>

                            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-100">
                                <span class="text-xs text-slate-500 font-semibold block uppercase tracking-wider">Number of Plots</span>
                                <span class="text-base font-bold text-slate-900 mt-1 block">{{ $property->number_of_plots ?: '1' }}</span>
                            </div>

                            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-100">
                                <span class="text-xs text-slate-500 font-semibold block uppercase tracking-wider">Permitted Land Use</span>
                                <span class="text-base font-bold text-slate-900 mt-1 block">{{ $property->land_use ?: 'Residential' }}</span>
                            </div>

                            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-100">
                                <span class="text-xs text-slate-500 font-semibold block uppercase tracking-wider">Topography</span>
                                <span class="text-base font-bold text-slate-900 mt-1 block">{{ $property->topography ?: '100% Dry Land' }}</span>
                            </div>
                        </div>

                        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-xs text-emerald-800 flex items-center gap-3">
                            <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                            <span>
                                <strong>Survey Coordinated:</strong> {{ $property->has_survey ? 'Registered and verified survey plan on file with beacon coordinates.' : 'Survey check and charting available upon physical inspection.' }}
                            </span>
                        </div>
                    @else
                        <!-- Building Specs -->
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-sm">
                            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-100 text-center">
                                <span class="text-xs text-slate-500 font-semibold block uppercase">Bedrooms</span>
                                <span class="text-2xl font-black text-slate-900 mt-1 block">{{ $property->bedrooms ?? '—' }}</span>
                            </div>

                            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-100 text-center">
                                <span class="text-xs text-slate-500 font-semibold block uppercase">Bathrooms</span>
                                <span class="text-2xl font-black text-slate-900 mt-1 block">{{ $property->bathrooms ?? '—' }}</span>
                            </div>

                            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-100 text-center">
                                <span class="text-xs text-slate-500 font-semibold block uppercase">Toilets</span>
                                <span class="text-2xl font-black text-slate-900 mt-1 block">{{ $property->toilets ?? '—' }}</span>
                            </div>

                            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-100 text-center">
                                <span class="text-xs text-slate-500 font-semibold block uppercase">Car Parking</span>
                                <span class="text-2xl font-black text-slate-900 mt-1 block">{{ $property->parking_spaces ?? '—' }}</span>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-100 flex justify-between items-center">
                                <span class="text-slate-500 uppercase font-semibold">Building Condition:</span>
                                <strong class="text-slate-900">{{ $property->building_condition ?: 'Newly Built' }}</strong>
                            </div>
                            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-100 flex justify-between items-center">
                                <span class="text-slate-500 uppercase font-semibold">Number of Floors:</span>
                                <strong class="text-slate-900">{{ $property->floors ?: '1' }} Storey</strong>
                            </div>
                        </div>

                        @if (!empty($property->features))
                            <div class="pt-4 border-t border-slate-100">
                                <h3 class="text-xs font-bold text-slate-700 uppercase tracking-wider mb-3">
                                    Amenities & Features
                                </h3>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs">
                                    @foreach ($property->features as $feature)
                                        <div class="flex items-center gap-2 p-2 rounded-lg bg-emerald-50/50 text-slate-700">
                                            <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                            </svg>
                                            <span>{{ $feature }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @endif
                </div>

                <!-- Full Description -->
                <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-xs space-y-4">
                    <h2 class="text-lg font-bold text-slate-900 border-b border-slate-100 pb-3">
                        About This Property
                    </h2>
                    <div class="text-sm text-slate-700 leading-relaxed whitespace-pre-line">
                        {{ $property->description }}
                    </div>
                </div>

                <!-- Approved Public Documents (If Any) -->
                @if ($property->publicDocuments->isNotEmpty())
                    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-xs space-y-4">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                            <h2 class="text-lg font-bold text-slate-900">
                                Verified Public Documents
                            </h2>
                            <span class="text-xs text-emerald-700 font-semibold bg-emerald-100 px-2.5 py-0.5 rounded-full">
                                Authorized for Download
                            </span>
                        </div>
                        <p class="text-xs text-slate-500">
                            The following property documentation has been reviewed and approved for public inspection download.
                        </p>

                        <div class="space-y-3">
                            @foreach ($property->publicDocuments as $doc)
                                <div class="flex items-center justify-between p-4 rounded-2xl bg-slate-50 border border-slate-200">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <h4 class="text-sm font-bold text-slate-900">{{ $doc->title }}</h4>
                                            <p class="text-2xs text-slate-500">{{ $doc->document_type }} • {{ $doc->formatted_size }}</p>
                                        </div>
                                    </div>

                                    <a href="{{ route('properties.documents.download', [$property->slug, $doc]) }}"
                                       class="px-4 py-2 rounded-xl bg-slate-900 hover:bg-emerald-600 text-white text-xs font-bold transition-colors flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                        </svg>
                                        Download
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

            </div>

            <!-- Right: Sticky Price & Direct Contact Card (4 Cols) -->
            <div class="lg:col-span-4 sticky top-28 space-y-6">

                <!-- Primary Price & Direct Agent Box -->
                <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-xl space-y-6">
                    <div>
                        <span class="text-xs uppercase font-bold text-slate-400 tracking-wider">Verified Asking Price</span>
                        <div class="text-3xl font-black text-slate-900 font-mono tracking-tight mt-1">
                            {{ $property->formatted_price }}
                        </div>
                        <span class="inline-block mt-1 text-2xs font-semibold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded">
                            Direct Agency Terms • Zero Agency Multi-Tiers
                        </span>
                    </div>

                    <!-- Direct Contact CTAs -->
                    <div class="space-y-3 pt-4 border-t border-slate-100">
                        @php
                            $waMessage = "Hello Victor, I am interested in property [{$property->reference}] - {$property->title}. Can we arrange an inspection?";
                            $waUrl = "https://wa.me/2348000000000?text=" . urlencode($waMessage);
                        @endphp

                        <a href="{{ $waUrl }}" target="_blank" rel="noopener noreferrer"
                           class="w-full py-3.5 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-center text-sm shadow-lg shadow-emerald-600/30 transition-all flex items-center justify-center gap-2">
                            <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24">
                                <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
                            </svg>
                            Enquire on WhatsApp
                        </a>

                        <a href="tel:+2348000000000"
                           class="w-full py-3.5 px-4 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-center text-sm transition-all flex items-center justify-center gap-2">
                            <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                            Direct Phone Call
                        </a>
                    </div>

                    <!-- Agent Assurance -->
                    <div class="pt-6 border-t border-slate-100 flex items-center gap-3.5">
                        <div class="w-11 h-11 rounded-full bg-slate-900 text-white font-black flex items-center justify-center shrink-0">
                            VA
                        </div>
                        <div>
                            <span class="block text-xs font-bold text-slate-900">Victor Akande</span>
                            <span class="block text-2xs text-slate-500">Lead Independent Real Estate Advisor</span>
                        </div>
                    </div>
                </div>

                <!-- Online Enquiry Form Card -->
                <div id="enquire" class="bg-white rounded-3xl p-6 sm:p-7 border border-slate-200 shadow-xl space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <div>
                            <h3 class="text-sm font-bold text-slate-900">Send Direct Website Enquiry</h3>
                            <p class="text-2xs text-slate-500 mt-0.5">Get a prompt personal response from Victor</p>
                        </div>
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
                    </div>

                    @if (session('enquiry_success'))
                        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs flex items-start gap-2.5">
                            <svg class="w-4 h-4 text-emerald-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <p>{{ session('enquiry_success') }}</p>
                        </div>
                    @endif

                    @if ($errors->has('enquiry_throttle'))
                        <div class="p-3.5 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 text-xs">
                            {{ $errors->first('enquiry_throttle') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('enquiries.store') }}" class="space-y-3.5">
                        @csrf
                        <input type="hidden" name="property_id" value="{{ $property->id }}">

                        <!-- Honeypot anti-spam bot trap -->
                        <div class="hidden" aria-hidden="true">
                            <input type="text" name="bot_check" tabindex="-1" autocomplete="off">
                        </div>

                        <div>
                            <label class="block text-2xs font-bold uppercase tracking-wider text-slate-600 mb-1">
                                Full Name <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" name="name" value="{{ old('name') }}" required
                                   placeholder="e.g. Chief Babatunde Adeleke"
                                   class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-900 placeholder-slate-400 focus:outline-hidden focus:bg-white focus:border-emerald-600 transition-colors">
                            @error('name')
                                <p class="text-3xs text-rose-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-2xs font-bold uppercase tracking-wider text-slate-600 mb-1">
                                Phone Number <span class="text-rose-500">*</span>
                            </label>
                            <input type="tel" name="phone" value="{{ old('phone') }}" required
                                   placeholder="e.g. 0803 123 4567 or +234..."
                                   class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-900 placeholder-slate-400 focus:outline-hidden focus:bg-white focus:border-emerald-600 transition-colors">
                            @error('phone')
                                <p class="text-3xs text-rose-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-2xs font-bold uppercase tracking-wider text-slate-600 mb-1">
                                Email Address <span class="text-slate-400 font-normal">(Optional)</span>
                            </label>
                            <input type="email" name="email" value="{{ old('email') }}"
                                   placeholder="e.g. name@example.com"
                                   class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-900 placeholder-slate-400 focus:outline-hidden focus:bg-white focus:border-emerald-600 transition-colors">
                            @error('email')
                                <p class="text-3xs text-rose-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-2xs font-bold uppercase tracking-wider text-slate-600 mb-1">
                                Your Message or Inquiry <span class="text-rose-500">*</span>
                            </label>
                            <textarea name="message" rows="3" required
                                      class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-900 placeholder-slate-400 focus:outline-hidden focus:bg-white focus:border-emerald-600 transition-colors">{{ old('message', "I am interested in property [{$property->reference}] - {$property->title}. Please provide more information and let me know available inspection dates.") }}</textarea>
                            @error('message')
                                <p class="text-3xs text-rose-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <button type="submit"
                                class="w-full py-3 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-md shadow-emerald-700/20 transition-all flex items-center justify-center gap-1.5 cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                            </svg>
                            Submit Enquiry
                        </button>
                    </form>
                </div>

                <!-- Inspection Notice -->
                <div class="bg-emerald-950/90 text-white rounded-3xl p-6 border border-emerald-800/80 shadow-md">
                    <div class="flex items-center gap-2 text-emerald-400 text-xs font-bold uppercase tracking-wider mb-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        Site Inspection Policy
                    </div>
                    <p class="text-xs text-slate-300 leading-relaxed">
                        Physical inspections are conducted Monday through Saturday. Meeting coordinates and inspection protocol will be confirmed directly upon WhatsApp or phone contact.
                    </p>
                </div>

            </div>

        </div>

        <!-- Related Properties -->
        @if ($relatedProperties->isNotEmpty())
            <div class="mt-16 pt-12 border-t border-slate-200">
                <h2 class="text-xl font-bold text-slate-900 mb-6">
                    Similar Properties in {{ $property->category->name }}
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @foreach ($relatedProperties as $rel)
                        <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-xs hover:shadow-md transition-shadow">
                            <a href="{{ route('properties.show', $rel->slug) }}" class="block h-44 bg-slate-900 overflow-hidden">
                                @if ($rel->cover_url)
                                    <img src="{{ $rel->cover_url }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-slate-600 text-xs font-bold">Photo Pending</div>
                                @endif
                            </a>
                            <div class="p-4">
                                <span class="font-mono text-3xs font-bold text-emerald-700">{{ $rel->reference }}</span>
                                <h3 class="text-sm font-bold text-slate-900 truncate mt-0.5">
                                    <a href="{{ route('properties.show', $rel->slug) }}" class="hover:text-emerald-600">
                                        {{ $rel->title }}
                                    </a>
                                </h3>
                                <p class="text-2xs text-slate-500 mt-1">{{ $rel->neighbourhood }}, {{ $rel->state }}</p>
                                <p class="text-sm font-black text-slate-900 font-mono mt-2">{{ $rel->formatted_price }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

    </div>
</div>
@endsection

