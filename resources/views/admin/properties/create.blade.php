@extends('layouts.admin')

@section('title', 'Add New Property')
@section('header', 'Create Property Listing')

@section('content')
<div class="max-w-5xl mx-auto space-y-6"
     x-data="{
         categories: {{ Js::from($categories) }},
         locationConfig: {{ Js::from($locationConfig) }},
         selectedCategoryId: '{{ old('category_id', $categories->first()->id ?? '') }}',
         selectedState: '{{ old('state', 'Lagos') }}',
         selectedLga: '{{ old('lga', '') }}',
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
         documents: [
             { id: 1, title: '', type: 'Certificate of Occupancy (C of O)', is_public: false }
         ],
         addDocument() {
             this.documents.push({
                 id: Date.now(),
                 title: '',
                 type: 'Certificate of Occupancy (C of O)',
                 is_public: false
             });
         },
         removeDocument(index) {
             if (this.documents.length > 1) {
                 this.documents.splice(index, 1);
             }
         },
         imagePreviews: [],
         coverIndex: 0,
         isDragging: false,
         get totalPhotoSize() {
             if (!this.imagePreviews.length) return '0 MB';
             const totalBytes = this.imagePreviews.reduce((acc, curr) => acc + (curr.file?.size || 0), 0);
             return (totalBytes / (1024 * 1024)).toFixed(1) + ' MB';
         },
         get coverPhoto() {
             return this.imagePreviews[this.coverIndex] || this.imagePreviews[0] || null;
         },
         addFiles(files) {
             const validFiles = Array.from(files).filter(file => file.type.startsWith('image/'));
             if (!validFiles.length) return;

             validFiles.forEach((file) => {
                 this.imagePreviews.push({
                     id: Math.random().toString(36).substr(2, 9),
                     name: file.name,
                     size: (file.size / (1024 * 1024)).toFixed(2) + ' MB',
                     url: URL.createObjectURL(file),
                     file: file
                 });
             });
             this.syncFileInput();
         },
         handleImageSelection(event) {
             this.addFiles(event.target.files);
         },
         handleDrop(event) {
             this.isDragging = false;
             if (event.dataTransfer && event.dataTransfer.files) {
                 this.addFiles(event.dataTransfer.files);
             }
         },
         removeImage(index) {
             if (this.imagePreviews[index]?.url) {
                 URL.revokeObjectURL(this.imagePreviews[index].url);
             }
             this.imagePreviews.splice(index, 1);
             if (this.coverIndex >= this.imagePreviews.length) {
                 this.coverIndex = Math.max(0, this.imagePreviews.length - 1);
             }
             this.syncFileInput();
         },
         clearAllImages() {
             this.imagePreviews.forEach(item => {
                 if (item.url) URL.revokeObjectURL(item.url);
             });
             this.imagePreviews = [];
             this.coverIndex = 0;
             this.syncFileInput();
         },
         setCover(index) {
             this.coverIndex = index;
         },
         syncFileInput() {
             if (window.DataTransfer && this.$refs.fileInput) {
                 const dt = new DataTransfer();
                 this.imagePreviews.forEach(item => {
                     if (item.file) dt.items.add(item.file);
                 });
                 this.$refs.fileInput.files = dt.files;
             }
         }
     }">

    <div class="flex items-center justify-between">
        <div>
            <a href="{{ route('admin.properties.index') }}" class="text-xs text-slate-400 hover:text-emerald-400 flex items-center gap-1 mb-2">
                &larr; Back to Listings
            </a>
            <h2 class="text-2xl font-bold text-white tracking-tight">Add New Property Listing</h2>
            <p class="text-xs text-slate-400 mt-1">
                Reference number will be auto-generated sequentially upon creation (e.g., LAND-0001 or PROP-0001).
            </p>
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

    <form method="POST" action="{{ route('admin.properties.store') }}" enctype="multipart/form-data" class="space-y-8">
        @csrf

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
                            <option value="{{ $cat->id }}">{{ $cat->name }} (Prefix: {{ $cat->code_prefix }})</option>
                        @endforeach
                    </select>
                </div>

                <!-- Subtype -->
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">
                        Subtype / Classification
                    </label>
                    <input type="text" name="property_type" value="{{ old('property_type') }}"
                           placeholder="e.g. 5-Bedroom Detached Duplex, Residential Plot, Office Plaza"
                           class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-xs text-white placeholder-slate-500 focus:outline-hidden focus:border-emerald-500">
                </div>

                <!-- Status -->
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">
                        Initial Listing Status <span class="text-rose-400">*</span>
                    </label>
                    <select name="status" required
                            class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-xs text-white focus:outline-hidden focus:border-emerald-500">
                        @foreach (\App\Models\Property::STATUSES as $code => $label)
                            <option value="{{ $code }}" {{ old('status', 'draft') === $code ? 'selected' : '' }}>
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
                <input type="text" name="title" value="{{ old('title') }}" required
                       placeholder="e.g. Luxury 4-Bedroom Fully Detached Duplex with BQ"
                       class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-sm text-white placeholder-slate-500 focus:outline-hidden focus:border-emerald-500">
            </div>

            <!-- Price & Featured -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 items-center">
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">
                        Price (₦ Naira) <span class="text-rose-400">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400 text-xs font-bold">₦</span>
                        <input type="number" step="0.01" name="price" value="{{ old('price') }}" required
                               placeholder="85000000"
                               class="w-full pl-8 pr-4 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-xs font-mono text-emerald-400 placeholder-slate-500 focus:outline-hidden focus:border-emerald-500">
                    </div>
                </div>

                <div class="pt-5">
                    <label class="flex items-center gap-3 cursor-pointer select-none">
                        <input type="checkbox" name="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }}
                               class="rounded border-slate-700 bg-slate-950 text-emerald-600 focus:ring-emerald-500 h-4 w-4">
                        <div>
                            <span class="text-xs font-semibold text-white">Mark as Featured Property</span>
                            <span class="block text-2xs text-slate-400">Featured properties will appear prominently on the public homepage showcase</span>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Description -->
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">
                    Detailed Property Description <span class="text-rose-400">*</span>
                </label>
                <textarea name="description" rows="5" required
                          placeholder="Provide detailed description of the property, structural highlights, infrastructure, accessibility, and documentation."
                          class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-xs text-white placeholder-slate-500 focus:outline-hidden focus:border-emerald-500">{{ old('description') }}</textarea>
            </div>
        </div>

        <!-- 2. Nigerian Location -->
        <div class="p-6 rounded-2xl bg-slate-900 border border-slate-800 space-y-6">
            <h3 class="text-sm font-bold text-white uppercase tracking-wider pb-3 border-b border-slate-800 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                2. Location Information (Nigeria)
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- State -->
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">
                        State <span class="text-rose-400">*</span>
                    </label>
                    <input type="text" name="state" x-model="selectedState" required list="state-options"
                           placeholder="e.g. Lagos"
                           class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-xs text-white focus:outline-hidden focus:border-emerald-500">
                    <datalist id="state-options">
                        <template x-for="state in Object.keys(locationConfig.states || {})" :key="state">
                            <option :value="state"></option>
                        </template>
                    </datalist>
                </div>

                <!-- LGA -->
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">
                        Local Government Area (LGA) <span class="text-rose-400">*</span>
                    </label>
                    <input type="text" name="lga" x-model="selectedLga" required list="lga-options"
                           placeholder="e.g. Eti-Osa"
                           class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-xs text-white focus:outline-hidden focus:border-emerald-500">
                    <datalist id="lga-options">
                        <template x-for="lga in currentLgas" :key="lga">
                            <option :value="lga"></option>
                        </template>
                    </datalist>
                </div>

                <!-- Neighbourhood -->
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">
                        Neighbourhood / District <span class="text-rose-400">*</span>
                    </label>
                    <input type="text" name="neighbourhood" value="{{ old('neighbourhood') }}" required list="neighbourhood-options"
                           placeholder="e.g. Lekki Phase 1"
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
                    Street Address / Landmark (Optional)
                </label>
                <input type="text" name="street_address" value="{{ old('street_address') }}"
                       placeholder="e.g. Off Admiralty Way, Near Freedom Way"
                       class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-xs text-white placeholder-slate-500 focus:outline-hidden focus:border-emerald-500">
            </div>
        </div>

        <!-- 3A. Land Specific Attributes (Dynamic Alpine Switch) -->
        <div x-show="isLand()" x-transition class="p-6 rounded-2xl bg-slate-900 border border-emerald-500/30 space-y-6">
            <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                <h3 class="text-sm font-bold text-white uppercase tracking-wider flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                    3. Land Attributes & Title Documentation
                </h3>
                <span class="px-2.5 py-1 rounded-full bg-emerald-950 text-emerald-400 text-2xs font-bold border border-emerald-800">
                    Land Category Active
                </span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">
                        Plot Size
                    </label>
                    <input type="text" name="plot_size" value="{{ old('plot_size') }}"
                           placeholder="e.g. 600 sqm, 1 Acre, 2 Hectares"
                           class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-xs text-white placeholder-slate-500 focus:outline-hidden focus:border-emerald-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">
                        Plot Dimensions
                    </label>
                    <input type="text" name="plot_dimensions" value="{{ old('plot_dimensions') }}"
                           placeholder="e.g. 60ft x 120ft"
                           class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-xs text-white placeholder-slate-500 focus:outline-hidden focus:border-emerald-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">
                        Number of Plots
                    </label>
                    <input type="number" step="0.5" name="number_of_plots" value="{{ old('number_of_plots') }}"
                           placeholder="e.g. 1, 2.5, 6"
                           class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-xs text-white placeholder-slate-500 focus:outline-hidden focus:border-emerald-500">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                <!-- Title Type -->
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">
                        Title / Documentation Type
                    </label>
                    <select name="title_type" class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-xs text-white focus:outline-hidden focus:border-emerald-500">
                        <option value="">Select Title Document</option>
                        @foreach (config('nigerian_locations.title_types', []) as $title)
                            <option value="{{ $title }}" {{ old('title_type') === $title ? 'selected' : '' }}>
                                {{ $title }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Land Use -->
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">
                        Permitted Land Use
                    </label>
                    <select name="land_use" class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-xs text-white focus:outline-hidden focus:border-emerald-500">
                        <option value="">Select Land Use</option>
                        @foreach (config('nigerian_locations.land_uses', []) as $use)
                            <option value="{{ $use }}" {{ old('land_use') === $use ? 'selected' : '' }}>
                                {{ $use }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Topography -->
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">
                        Topography
                    </label>
                    <select name="topography" class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-xs text-white focus:outline-hidden focus:border-emerald-500">
                        <option value="">Select Topography</option>
                        @foreach (config('nigerian_locations.topographies', []) as $topo)
                            <option value="{{ $topo }}" {{ old('topography') === $topo ? 'selected' : '' }}>
                                {{ $topo }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="pt-2">
                <label class="flex items-center gap-3 cursor-pointer select-none">
                    <input type="checkbox" name="has_survey" value="1" {{ old('has_survey') ? 'checked' : '' }}
                           class="rounded border-slate-700 bg-slate-950 text-emerald-600 focus:ring-emerald-500 h-4 w-4">
                    <span class="text-xs font-semibold text-slate-300">Registered Survey Plan Available & Coordinated</span>
                </label>
            </div>
        </div>

        <!-- 3B. Building Specific Attributes (Dynamic Alpine Switch) -->
        <div x-show="!isLand()" x-transition class="p-6 rounded-2xl bg-slate-900 border border-sky-500/30 space-y-6">
            <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                <h3 class="text-sm font-bold text-white uppercase tracking-wider flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-sky-400"></span>
                    3. Building Specifications & Features
                </h3>
                <span class="px-2.5 py-1 rounded-full bg-sky-950 text-sky-400 text-2xs font-bold border border-sky-800">
                    Building Category Active
                </span>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-5 gap-4">
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Bedrooms</label>
                    <input type="number" min="0" name="bedrooms" value="{{ old('bedrooms') }}"
                           class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-xs text-white">
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Bathrooms</label>
                    <input type="number" min="0" name="bathrooms" value="{{ old('bathrooms') }}"
                           class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-xs text-white">
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Toilets</label>
                    <input type="number" min="0" name="toilets" value="{{ old('toilets') }}"
                           class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-xs text-white">
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Car Parking</label>
                    <input type="number" min="0" name="parking_spaces" value="{{ old('parking_spaces') }}"
                           class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-xs text-white">
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">Floors</label>
                    <input type="number" min="1" name="floors" value="{{ old('floors', 1) }}"
                           class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-xs text-white">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">
                    Building Condition
                </label>
                <select name="building_condition" class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-xs text-white focus:outline-hidden focus:border-emerald-500">
                    <option value="">Select Condition</option>
                    @foreach (config('nigerian_locations.building_conditions', []) as $condition)
                        <option value="{{ $condition }}" {{ old('building_condition') === $condition ? 'selected' : '' }}>
                            {{ $condition }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Features Checkboxes -->
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-3">
                    Key Features & Amenities
                </label>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-2.5">
                    @foreach (config('nigerian_locations.common_features', []) as $feature)
                        <label class="flex items-center gap-2.5 p-2 rounded-lg bg-slate-950 border border-slate-800 text-xs text-slate-300 cursor-pointer hover:border-slate-700">
                            <input type="checkbox" name="features[]" value="{{ $feature }}"
                                   {{ is_array(old('features')) && in_array($feature, old('features')) ? 'checked' : '' }}
                                   class="rounded border-slate-700 bg-slate-900 text-emerald-600 focus:ring-emerald-500 h-3.5 w-3.5">
                            <span class="truncate">{{ $feature }}</span>
                        </label>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- 4. Media: Property Images & Photography -->
        <div class="p-6 sm:p-8 rounded-2xl bg-slate-900 border border-slate-800 space-y-6 shadow-xl">
            <!-- Header with Status Pill -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between pb-4 border-b border-slate-800 gap-3">
                <div>
                    <h3 class="text-sm font-bold text-white uppercase tracking-wider flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 ring-4 ring-emerald-500/20"></span>
                        4. Property Photography & Visuals
                    </h3>
                    <p class="text-xs text-slate-400 mt-1">
                        High-resolution visual assets. The designated cover photo will be the primary visual across search results and listing cards.
                    </p>
                </div>
                <div class="flex items-center gap-2" x-show="imagePreviews.length > 0">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-950/80 border border-emerald-800/80 text-emerald-400">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <span x-text="imagePreviews.length + ' photo' + (imagePreviews.length > 1 ? 's' : '')"></span>
                        <span class="text-emerald-600">•</span>
                        <span x-text="totalPhotoSize"></span>
                    </span>
                    <button type="button" @click="clearAllImages()"
                            class="text-xs text-slate-400 hover:text-rose-400 transition-colors px-2.5 py-1 rounded-lg hover:bg-slate-800/60 cursor-pointer">
                        Clear all
                    </button>
                </div>
            </div>

            <!-- Hidden input for backend cover_index -->
            <input type="hidden" name="cover_index" :value="coverIndex">

            <!-- Hidden file input for pristine styling -->
            <input type="file" name="images[]" multiple accept="image/jpeg,image/png,image/webp" x-ref="fileInput" @change="handleImageSelection($event)" class="hidden">

            <!-- Modern Interactive Dropzone -->
            <div @click="$refs.fileInput.click()"
                 @dragover.prevent="isDragging = true"
                 @dragleave.prevent="isDragging = false"
                 @drop.prevent="handleDrop($event)"
                 class="relative group rounded-2xl border-2 border-dashed transition-all duration-200 p-8 sm:p-10 text-center cursor-pointer flex flex-col items-center justify-center gap-3"
                 :class="isDragging ? 'border-emerald-400 bg-emerald-950/20 shadow-lg shadow-emerald-950/50' : 'border-slate-700/80 hover:border-emerald-500/60 bg-slate-950/40 hover:bg-slate-950/70'">

                <!-- Glowing Icon -->
                <div class="w-16 h-16 rounded-2xl bg-gradient-to-tr from-slate-900 to-slate-800 border border-slate-700 flex items-center justify-center text-emerald-400 group-hover:scale-105 group-hover:border-emerald-500/50 transition-all shadow-md">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 4v16m8-8H4" />
                        </svg>
                </div>

                <div class="space-y-1">
                    <p class="text-sm font-semibold text-white">
                        <span class="text-emerald-400 group-hover:underline underline-offset-4">Click to browse</span> or drag and drop photos here
                    </p>
                    <p class="text-xs text-slate-400">
                        Supports high-resolution JPG, PNG, and WebP (up to 5MB each)
                    </p>
                </div>

                <div class="flex items-center gap-2 pt-1 text-2xs text-slate-500">
                    <span class="px-2 py-0.5 rounded-md bg-slate-900 border border-slate-800">16:9 Landscape Ideal</span>
                    <span>•</span>
                    <span class="px-2 py-0.5 rounded-md bg-slate-900 border border-slate-800">Multiple selection supported</span>
                </div>
            </div>

            <!-- Previews Workspace (Rendered if images are selected) -->
            <div x-show="imagePreviews.length > 0" x-cloak class="space-y-6 pt-2">
                <!-- Cover Photo Spotlight -->
                <div class="p-4 sm:p-5 rounded-2xl bg-slate-950/80 border border-emerald-500/40 relative overflow-hidden">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-2">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-2xs font-extrabold uppercase tracking-wider bg-emerald-500 text-slate-950 shadow-sm">
                                <svg class="w-3 h-3 fill-current" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                </svg>
                                Primary Cover Photo
                            </span>
                            <span class="text-2xs text-slate-400">Featured visual shown on homepage & search cards</span>
                        </div>
                        <button type="button" @click="$refs.fileInput.click()" class="text-xs font-semibold text-emerald-400 hover:text-emerald-300 flex items-center gap-1 cursor-pointer">
                            + Add more photos
                        </button>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-12 gap-5 items-center">
                        <div class="md:col-span-7 lg:col-span-8 rounded-xl overflow-hidden aspect-16/9 bg-slate-900 border border-slate-800 relative shadow-inner">
                            <template x-if="coverPhoto">
                                <img :src="coverPhoto.url" :alt="coverPhoto.name" class="w-full h-full object-cover">
                            </template>
                        </div>
                        <div class="md:col-span-5 lg:col-span-4 space-y-3">
                            <div>
                                <span class="text-2xs uppercase tracking-wider text-slate-500 font-bold block mb-1">Selected Cover Image</span>
                                <p class="text-sm font-bold text-white truncate" x-text="coverPhoto ? coverPhoto.name : ''"></p>
                                <p class="text-xs text-slate-400 font-mono mt-0.5" x-text="coverPhoto ? coverPhoto.size : ''"></p>
                            </div>

                            <p class="text-xs text-slate-400 leading-relaxed">
                                To choose a different cover photo, click the <strong class="text-emerald-400">"★ Set as Cover"</strong> button on any photo in the gallery below.
                            </p>

                            <div class="pt-2">
                                <button type="button" @click="removeImage(coverIndex)"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-rose-950/60 hover:bg-rose-900/60 border border-rose-800/80 text-rose-300 text-xs font-semibold transition-colors cursor-pointer">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                    Remove this photo
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Gallery Grid of All Photos -->
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-300">
                            All Uploaded Photos (<span x-text="imagePreviews.length"></span>)
                        </span>
                        <span class="text-2xs text-slate-500">
                            Hover over any image to set as cover or remove
                        </span>
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                        <template x-for="(item, idx) in imagePreviews" :key="item.id">
                            <div class="relative group rounded-xl overflow-hidden bg-slate-950 border transition-all duration-200 shadow-md flex flex-col"
                                 :class="coverIndex === idx ? 'border-emerald-500 ring-2 ring-emerald-500/40 shadow-emerald-950/50' : 'border-slate-800 hover:border-slate-600'">

                                <!-- Photo Frame (16:9 Aspect Ratio) -->
                                <div class="aspect-16/9 w-full bg-slate-900 relative overflow-hidden">
                                    <img :src="item.url" :alt="item.name" class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105">

                                    <!-- Index Chip -->
                                    <span class="absolute top-2 left-2 px-2 py-0.5 rounded-md text-3xs font-mono font-bold bg-slate-950/80 backdrop-blur-xs text-slate-300 border border-slate-800">
                                        #<span x-text="idx + 1"></span>
                                    </span>

                                    <!-- Cover Indicator -->
                                    <span x-show="coverIndex === idx"
                                          class="absolute top-2 right-2 px-2 py-0.5 rounded-md text-3xs font-extrabold uppercase tracking-wider bg-emerald-500 text-slate-950 shadow">
                                        ★ Cover
                                    </span>

                                    <!-- Hover Glassmorphism Overlay with Quick Actions -->
                                    <div class="absolute inset-0 bg-slate-950/70 backdrop-blur-xs opacity-0 group-hover:opacity-100 transition-opacity duration-200 flex items-center justify-center gap-2 p-2">
                                        <button type="button"
                                                x-show="coverIndex !== idx"
                                                @click="setCover(idx)"
                                                title="Set as primary cover photo"
                                                class="px-2.5 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white text-2xs font-bold shadow-md transition-all flex items-center gap-1 cursor-pointer">
                                            <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                            </svg>
                                            Set Cover
                                        </button>

                                        <button type="button"
                                                @click="removeImage(idx)"
                                                title="Delete photo"
                                                class="w-8 h-8 rounded-lg bg-rose-950/80 hover:bg-rose-600 border border-rose-800/80 text-rose-300 hover:text-white flex items-center justify-center transition-all cursor-pointer shadow-md">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>

                                <!-- Metadata Footer -->
                                <div class="p-2.5 bg-slate-950/90 border-t border-slate-900 flex items-center justify-between text-2xs">
                                    <span class="text-white font-medium truncate max-w-[120px]" :title="item.name" x-text="item.name"></span>
                                    <span class="text-slate-400 font-mono text-3xs shrink-0" x-text="item.size"></span>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>

        <!-- 5. Confidential & Public Property Documents -->
        <div class="p-6 rounded-2xl bg-slate-900 border border-slate-800 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                <h3 class="text-sm font-bold text-white uppercase tracking-wider flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                    5. Property Documents & Attachments
                </h3>
                <button type="button" @click="addDocument()" class="text-xs font-semibold text-emerald-400 hover:text-emerald-300 flex items-center gap-1 cursor-pointer">
                    + Add Another Document
                </button>
            </div>

            <p class="text-xs text-slate-400">
                Upload survey plans, deed drafts, or land title extracts (PDF, JPG up to 10MB). <strong class="text-slate-200">Documents are private by default</strong> and will not be accessible to website visitors unless toggled public.
            </p>

            <div class="space-y-4">
                <template x-for="(doc, index) in documents" :key="doc.id">
                    <div class="p-4 rounded-xl bg-slate-950 border border-slate-800 space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-slate-300" x-text="'Document #' + (index + 1)"></span>
                            <button type="button" @click="removeDocument(index)" x-show="documents.length > 1" class="text-2xs text-rose-400 hover:text-rose-300">
                                Remove
                            </button>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                            <div>
                                <label class="block text-2xs uppercase text-slate-400 mb-1">Document Title</label>
                                <input type="text" :name="'documents[' + index + '][title]'"
                                       placeholder="e.g. Registered Survey Plan"
                                       class="w-full px-3 py-2 rounded-lg bg-slate-900 border border-slate-700 text-xs text-white">
                            </div>

                            <div>
                                <label class="block text-2xs uppercase text-slate-400 mb-1">Document Type</label>
                                <select :name="'documents[' + index + '][type]'" class="w-full px-3 py-2 rounded-lg bg-slate-900 border border-slate-700 text-xs text-white">
                                    @foreach (config('nigerian_locations.title_types', []) as $title)
                                        <option value="{{ $title }}">{{ $title }}</option>
                                    @endforeach
                                    <option value="Approved Building Plan">Approved Building Plan</option>
                                    <option value="Survey Plan">Survey Plan</option>
                                    <option value="Other">Other Supporting Document</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-2xs uppercase text-slate-400 mb-1">File Attachment (PDF / Image)</label>
                                <input type="file" :name="'documents[' + index + '][file]'" accept=".pdf,image/*"
                                       class="w-full text-xs text-slate-400 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:bg-slate-800 file:text-white">
                            </div>
                        </div>

                        <div class="pt-2 border-t border-slate-900">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" :name="'documents[' + index + '][is_public]'" value="1"
                                       class="rounded border-slate-700 bg-slate-900 text-emerald-600 focus:ring-emerald-500 h-3.5 w-3.5">
                                <span class="text-xs text-slate-400">Allow verified public visitors to download this document on the property details page</span>
                            </label>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- Submit Buttons -->
        <div class="flex items-center justify-end gap-4 pt-4">
            <a href="{{ route('admin.properties.index') }}" class="px-5 py-3 rounded-xl bg-slate-900 hover:bg-slate-800 text-slate-300 font-semibold text-xs border border-slate-800 transition-colors">
                Cancel
            </a>
            <button type="submit" class="px-8 py-3 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-lg shadow-emerald-950 transition-all cursor-pointer">
                Save & Create Property
            </button>
        </div>

    </form>

</div>
@endsection

