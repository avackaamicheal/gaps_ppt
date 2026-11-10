@extends('layouts.admin')

@section('title', 'Edit Property: ' . $property->reference)
@section('header', 'Edit Property Listing')

@section('content')
<div class="max-w-5xl mx-auto space-y-6"
     x-data="{
         categories: {{ Js::from($categories) }},
         locationConfig: {{ Js::from($locationConfig) }},
         selectedCategoryId: '{{ old('category_id', $property->category_id) }}',
         selectedState: '{{ old('state', $property->state) }}',
         selectedLga: '{{ old('lga', $property->lga) }}',
         isLand() {
             const cat = this.categories.find(c => String(c.id) === String(this.selectedCategoryId));
             return cat ? cat.slug === 'land' : false;
         },
         get currentLgas() {
             return (this.locationConfig.states && this.locationConfig.states[this.selectedState])
                 ? Object.keys(this.locationConfig.states[this.selectedState])
                 : [];
         },
         get currentNeighbourhoods() {
             if (!this.selectedState || !this.selectedLga) return [];
             const stateObj = this.locationConfig.states[this.selectedState];
             return (stateObj && stateObj[this.selectedLga]) ? stateObj[this.selectedLga] : [];
         },
         newDocuments: [],
         addNewDocument() {
             this.newDocuments.push({
                 id: Date.now(),
                 title: '',
                 type: 'Certificate of Occupancy (C of O)',
                 is_public: false
             });
         },
         removeNewDocument(index) {
             this.newDocuments.splice(index, 1);
         }
     }">

    <div class="flex items-center justify-between">
        <div>
            <a href="{{ route('admin.properties.index') }}" class="text-xs text-slate-400 hover:text-emerald-400 flex items-center gap-1 mb-2">
                &larr; Back to Listings
            </a>
            <div class="flex items-center gap-3">
                <h2 class="text-2xl font-bold text-white tracking-tight">Edit: {{ $property->title }}</h2>
                <span class="font-mono text-xs font-bold text-emerald-400 bg-emerald-950 px-2 py-0.5 rounded border border-emerald-800">
                    {{ $property->reference }}
                </span>
            </div>
        </div>

        <div class="flex items-center gap-2">
            @if ($property->status === 'published')
                <a href="{{ route('properties.show', $property->slug) }}" target="_blank" class="px-3 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-semibold border border-slate-700">
                    View Live Page &rarr;
                </a>
            @endif
        </div>
    </div>

    <!-- Error Alerts -->
    @if ($errors->any())
        <div class="p-4 rounded-2xl bg-rose-950/60 border border-rose-800 text-rose-300 text-xs space-y-1">
            <p class="font-bold">Please correct the following errors:</p>
            <ul class="list-disc list-inside space-y-0.5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.properties.update', $property) }}" enctype="multipart/form-data" class="space-y-8">
        @csrf
        @method('PUT')

        <!-- 1. Category & General Information -->
        <div class="p-6 rounded-2xl bg-slate-900 border border-slate-800 space-y-6">
            <h3 class="text-sm font-bold text-white uppercase tracking-wider pb-3 border-b border-slate-800 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                1. Basic Listing Details
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Category Selector -->
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">
                        Property Category <span class="text-rose-400">*</span>
                    </label>
                    <select name="category_id" x-model="selectedCategoryId" required
                            class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-xs text-white focus:outline-hidden focus:border-emerald-500">
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Subtype -->
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">
                        Subtype / Classification
                    </label>
                    <input type="text" name="property_type" value="{{ old('property_type', $property->property_type) }}"
                           placeholder="e.g. 5-Bedroom Detached Duplex"
                           class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-xs text-white focus:outline-hidden focus:border-emerald-500">
                </div>

                <!-- Status -->
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">
                        Listing Status <span class="text-rose-400">*</span>
                    </label>
                    <select name="status" required
                            class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-xs text-white focus:outline-hidden focus:border-emerald-500">
                        @foreach (\App\Models\Property::STATUSES as $code => $label)
                            <option value="{{ $code }}" {{ old('status', $property->status) === $code ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Title -->
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">
                    Property Title <span class="text-rose-400">*</span>
                </label>
                <input type="text" name="title" value="{{ old('title', $property->title) }}" required
                       class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-sm text-white focus:outline-hidden focus:border-emerald-500">
            </div>

            <!-- Price & Featured -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 items-center">
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">
                        Price (₦ Naira) <span class="text-rose-400">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400 text-xs font-bold">₦</span>
                        <input type="number" step="0.01" name="price" value="{{ old('price', $property->price) }}" required
                               class="w-full pl-8 pr-4 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-xs font-mono text-emerald-400 focus:outline-hidden focus:border-emerald-500">
                    </div>
                </div>

                <div class="pt-5">
                    <label class="flex items-center gap-3 cursor-pointer select-none">
                        <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $property->is_featured) ? 'checked' : '' }}
                               class="rounded border-slate-700 bg-slate-950 text-emerald-600 focus:ring-emerald-500 h-4 w-4">
                        <div>
                            <span class="text-xs font-semibold text-white">Featured Listing</span>
                            <span class="block text-2xs text-slate-400">Highlight in public homepage showcase</span>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Description -->
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">
                    Property Description <span class="text-rose-400">*</span>
                </label>
                <textarea name="description" rows="5" required
                          class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-xs text-white focus:outline-hidden focus:border-emerald-500">{{ old('description', $property->description) }}</textarea>
            </div>
        </div>

        <!-- 2. Location -->
        <div class="p-6 rounded-2xl bg-slate-900 border border-slate-800 space-y-6">
            <h3 class="text-sm font-bold text-white uppercase tracking-wider pb-3 border-b border-slate-800 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                2. Location Information (Nigeria)
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">
                        State <span class="text-rose-400">*</span>
                    </label>
                    <input type="text" name="state" x-model="selectedState" required list="state-options"
                           class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-xs text-white focus:outline-hidden focus:border-emerald-500">
                    <datalist id="state-options">
                        <template x-for="state in Object.keys(locationConfig.states || {})" :key="state">
                            <option :value="state"></option>
                        </template>
                    </datalist>
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">
                        LGA <span class="text-rose-400">*</span>
                    </label>
                    <input type="text" name="lga" x-model="selectedLga" required list="lga-options"
                           class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-xs text-white focus:outline-hidden focus:border-emerald-500">
                    <datalist id="lga-options">
                        <template x-for="lga in currentLgas" :key="lga">
                            <option :value="lga"></option>
                        </template>
                    </datalist>
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">
                        Neighbourhood <span class="text-rose-400">*</span>
                    </label>
                    <input type="text" name="neighbourhood" value="{{ old('neighbourhood', $property->neighbourhood) }}" required list="neighbourhood-options"
                           class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-xs text-white focus:outline-hidden focus:border-emerald-500">
                    <datalist id="neighbourhood-options">
                        <template x-for="hood in currentNeighbourhoods" :key="hood">
                            <option :value="hood"></option>
                        </template>
                    </datalist>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">
                    Street Address (Optional)
                </label>
                <input type="text" name="street_address" value="{{ old('street_address', $property->street_address) }}"
                       class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-xs text-white focus:outline-hidden focus:border-emerald-500">
            </div>
        </div>

        <!-- 3A. Land Specific Attributes -->
        <div x-show="isLand()" x-transition class="p-6 rounded-2xl bg-slate-900 border border-emerald-500/30 space-y-6">
            <h3 class="text-sm font-bold text-white uppercase tracking-wider pb-3 border-b border-slate-800 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                3. Land Attributes & Title Documentation
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Plot Size</label>
                    <input type="text" name="plot_size" value="{{ old('plot_size', $property->plot_size) }}"
                           placeholder="e.g. 600 sqm, 1 Acre"
                           class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-xs text-white">
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Plot Dimensions</label>
                    <input type="text" name="plot_dimensions" value="{{ old('plot_dimensions', $property->plot_dimensions) }}"
                           placeholder="e.g. 60ft x 120ft"
                           class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-xs text-white">
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Number of Plots</label>
                    <input type="number" step="0.5" name="number_of_plots" value="{{ old('number_of_plots', $property->number_of_plots) }}"
                           class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-xs text-white">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Title Type</label>
                    <select name="title_type" class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-xs text-white">
                        <option value="">Select Title Type</option>
                        @foreach (config('nigerian_locations.title_types', []) as $title)
                            <option value="{{ $title }}" {{ old('title_type', $property->title_type) === $title ? 'selected' : '' }}>
                                {{ $title }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Land Use</label>
                    <select name="land_use" class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-xs text-white">
                        <option value="">Select Land Use</option>
                        @foreach (config('nigerian_locations.land_uses', []) as $use)
                            <option value="{{ $use }}" {{ old('land_use', $property->land_use) === $use ? 'selected' : '' }}>
                                {{ $use }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Topography</label>
                    <select name="topography" class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-xs text-white">
                        <option value="">Select Topography</option>
                        @foreach (config('nigerian_locations.topographies', []) as $topo)
                            <option value="{{ $topo }}" {{ old('topography', $property->topography) === $topo ? 'selected' : '' }}>
                                {{ $topo }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="pt-2">
                <label class="flex items-center gap-3 cursor-pointer select-none">
                    <input type="checkbox" name="has_survey" value="1" {{ old('has_survey', $property->has_survey) ? 'checked' : '' }}
                           class="rounded border-slate-700 bg-slate-950 text-emerald-600 focus:ring-emerald-500 h-4 w-4">
                    <span class="text-xs font-semibold text-slate-300">Registered Survey Plan Available</span>
                </label>
            </div>
        </div>

        <!-- 3B. Building Specific Attributes -->
        <div x-show="!isLand()" x-transition class="p-6 rounded-2xl bg-slate-900 border border-sky-500/30 space-y-6">
            <h3 class="text-sm font-bold text-white uppercase tracking-wider pb-3 border-b border-slate-800 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-sky-400"></span>
                3. Building Specifications & Features
            </h3>

            <div class="grid grid-cols-2 sm:grid-cols-5 gap-4">
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Bedrooms</label>
                    <input type="number" min="0" name="bedrooms" value="{{ old('bedrooms', $property->bedrooms) }}"
                           class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-xs text-white">
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Bathrooms</label>
                    <input type="number" min="0" name="bathrooms" value="{{ old('bathrooms', $property->bathrooms) }}"
                           class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-xs text-white">
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Toilets</label>
                    <input type="number" min="0" name="toilets" value="{{ old('toilets', $property->toilets) }}"
                           class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-xs text-white">
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Parking</label>
                    <input type="number" min="0" name="parking_spaces" value="{{ old('parking_spaces', $property->parking_spaces) }}"
                           class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-xs text-white">
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Floors</label>
                    <input type="number" min="1" name="floors" value="{{ old('floors', $property->floors) }}"
                           class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-xs text-white">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Building Condition</label>
                <select name="building_condition" class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-xs text-white">
                    <option value="">Select Condition</option>
                    @foreach (config('nigerian_locations.building_conditions', []) as $cond)
                        <option value="{{ $cond }}" {{ old('building_condition', $property->building_condition) === $cond ? 'selected' : '' }}>
                            {{ $cond }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-3">Key Features & Amenities</label>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-2.5">
                    @php $savedFeatures = is_array($property->features) ? $property->features : []; @endphp
                    @foreach (config('nigerian_locations.common_features', []) as $feat)
                        <label class="flex items-center gap-2.5 p-2 rounded-lg bg-slate-950 border border-slate-800 text-xs text-slate-300 cursor-pointer">
                            <input type="checkbox" name="features[]" value="{{ $feat }}"
                                   {{ (is_array(old('features')) ? in_array($feat, old('features')) : in_array($feat, $savedFeatures)) ? 'checked' : '' }}
                                   class="rounded border-slate-700 bg-slate-900 text-emerald-600 focus:ring-emerald-500 h-3.5 w-3.5">
                            <span class="truncate">{{ $feat }}</span>
                        </label>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- 4. Manage Existing & New Images -->
        <div class="p-6 rounded-2xl bg-slate-900 border border-slate-800 space-y-6">
            <h3 class="text-sm font-bold text-white uppercase tracking-wider pb-3 border-b border-slate-800 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                4. Property Images ({{ $property->images->count() }} attached)
            </h3>

            @if ($property->images->isNotEmpty())
                <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-6 gap-4">
                    @foreach ($property->images as $img)
                        <div class="relative rounded-xl overflow-hidden border {{ $img->is_cover ? 'border-emerald-500 ring-2 ring-emerald-500/50' : 'border-slate-800' }} bg-slate-950 group">
                            <img src="{{ $img->url }}" class="w-full h-24 object-cover">

                            <div class="p-2 space-y-1.5 text-2xs bg-slate-950">
                                <label class="flex items-center gap-1.5 text-slate-300 cursor-pointer">
                                    <input type="radio" name="cover_image_id" value="{{ $img->id }}" {{ $img->is_cover ? 'checked' : '' }}
                                           class="text-emerald-600 focus:ring-emerald-500">
                                    <span>Cover</span>
                                </label>

                                <label class="flex items-center gap-1.5 text-rose-400 cursor-pointer">
                                    <input type="checkbox" name="delete_images[]" value="{{ $img->id }}" class="text-rose-600">
                                    <span>Delete</span>
                                </label>
                            </div>

                            @if ($img->is_cover)
                                <span class="absolute top-1.5 left-1.5 px-1.5 py-0.5 rounded text-3xs font-extrabold bg-emerald-600 text-white">
                                    COVER
                                </span>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif

            <div class="pt-2">
                <label class="block text-xs font-semibold text-slate-300 mb-2">Upload Additional Images</label>
                <div class="p-4 rounded-xl border border-dashed border-slate-700 bg-slate-950/60 text-center">
                    <input type="file" name="images[]" multiple accept="image/*" class="w-full text-xs text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-emerald-600 file:text-white cursor-pointer">
                </div>
            </div>
        </div>

        <!-- 5. Manage Existing & New Documents -->
        <div class="p-6 rounded-2xl bg-slate-900 border border-slate-800 space-y-6">
            <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                <h3 class="text-sm font-bold text-white uppercase tracking-wider flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                    5. Property Documents ({{ $property->documents->count() }} attached)
                </h3>
                <button type="button" @click="addNewDocument()" class="text-xs font-semibold text-emerald-400 hover:text-emerald-300 cursor-pointer">
                    + Add New Document
                </button>
            </div>

            @if ($property->documents->isNotEmpty())
                <div class="space-y-2.5">
                    @foreach ($property->documents as $doc)
                        <div class="flex items-center justify-between p-3 rounded-xl bg-slate-950 border border-slate-800 text-xs">
                            <div class="flex items-center gap-3">
                                <span class="p-2 rounded-lg bg-slate-900 text-slate-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                </span>
                                <div>
                                    <p class="font-bold text-white">{{ $doc->title }}</p>
                                    <p class="text-2xs text-slate-500">
                                        {{ $doc->document_type }} • {{ $doc->formatted_size }} •
                                        @if ($doc->is_public)
                                            <span class="text-emerald-400 font-semibold">Public Download</span>
                                        @else
                                            <span class="text-amber-400 font-semibold">Private / Confidential</span>
                                        @endif
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-center gap-3">
                                <a href="{{ route('admin.properties.documents.download', [$property, $doc]) }}" class="px-2.5 py-1 rounded-lg bg-slate-800 text-slate-300 hover:text-white text-2xs font-semibold">
                                    Download
                                </a>

                                <label class="flex items-center gap-1.5 text-rose-400 text-2xs cursor-pointer">
                                    <input type="checkbox" name="delete_documents[]" value="{{ $doc->id }}" class="text-rose-600">
                                    <span>Delete</span>
                                </label>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            <!-- Dynamic New Document Rows -->
            <div class="space-y-3 pt-2">
                <template x-for="(doc, idx) in newDocuments" :key="doc.id">
                    <div class="p-4 rounded-xl bg-slate-950 border border-slate-800 space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-slate-300" x-text="'New Document #' + (idx + 1)"></span>
                            <button type="button" @click="removeNewDocument(idx)" class="text-2xs text-rose-400">Remove</button>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                            <input type="text" :name="'documents[' + idx + '][title]'" placeholder="Document Title" class="px-3 py-2 rounded-lg bg-slate-900 border border-slate-700 text-xs text-white">
                            <select :name="'documents[' + idx + '][type]'" class="px-3 py-2 rounded-lg bg-slate-900 border border-slate-700 text-xs text-white">
                                @foreach (config('nigerian_locations.title_types', []) as $title)
                                    <option value="{{ $title }}">{{ $title }}</option>
                                @endforeach
                                <option value="Other">Other Supporting Document</option>
                            </select>
                            <input type="file" :name="'documents[' + idx + '][file]'" accept=".pdf,image/*" class="text-xs text-slate-400 file:mr-2 file:py-1 file:px-2 file:rounded file:bg-slate-800 file:text-white">
                        </div>

                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" :name="'documents[' + idx + '][is_public]'" value="1" class="text-emerald-600">
                            <span class="text-2xs text-slate-400">Allow public visitors to download this file</span>
                        </label>
                    </div>
                </template>
            </div>
        </div>

        <!-- Submit Bar -->
        <div class="flex items-center justify-between pt-4">
            <a href="{{ route('admin.properties.index') }}" class="px-5 py-3 rounded-xl bg-slate-900 text-slate-300 text-xs font-semibold border border-slate-800">
                Cancel
            </a>
            <button type="submit" class="px-8 py-3 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-lg shadow-emerald-950 transition-all cursor-pointer">
                Save Changes
            </button>
        </div>

    </form>

</div>
@endsection
