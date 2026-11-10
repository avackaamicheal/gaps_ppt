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
         },
         newImagePreviews: [],
         isDraggingNew: false,
         get totalNewPhotoSize() {
             if (!this.newImagePreviews.length) return '0 MB';
             const totalBytes = this.newImagePreviews.reduce((acc, curr) => acc + (curr.file?.size || 0), 0);
             return (totalBytes / (1024 * 1024)).toFixed(1) + ' MB';
         },
         addNewFiles(files) {
             const validFiles = Array.from(files).filter(file => file.type.startsWith('image/'));
             if (!validFiles.length) return;

             validFiles.forEach((file) => {
                 this.newImagePreviews.push({
                     id: Math.random().toString(36).substr(2, 9),
                     name: file.name,
                     size: (file.size / (1024 * 1024)).toFixed(2) + ' MB',
                     url: URL.createObjectURL(file),
                     file: file
                 });
             });
             this.syncNewFileInput();
         },
         handleNewImageSelection(event) {
             this.addNewFiles(event.target.files);
         },
         handleNewDrop(event) {
             this.isDraggingNew = false;
             if (event.dataTransfer && event.dataTransfer.files) {
                 this.addNewFiles(event.dataTransfer.files);
             }
         },
         removeNewImage(index) {
             if (this.newImagePreviews[index]?.url) {
                 URL.revokeObjectURL(this.newImagePreviews[index].url);
             }
             this.newImagePreviews.splice(index, 1);
             this.syncNewFileInput();
         },
         clearAllNewImages() {
             this.newImagePreviews.forEach(item => {
                 if (item.url) URL.revokeObjectURL(item.url);
             });
             this.newImagePreviews = [];
             this.syncNewFileInput();
         },
         syncNewFileInput() {
             if (window.DataTransfer && this.$refs.newFileInput) {
                 const dt = new DataTransfer();
                 this.newImagePreviews.forEach(item => {
                     if (item.file) dt.items.add(item.file);
                 });
                 this.$refs.newFileInput.files = dt.files;
             }
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
        <div class="p-6 sm:p-8 rounded-2xl bg-slate-900 border border-slate-800 space-y-6 shadow-xl">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between pb-4 border-b border-slate-800 gap-3">
                <div>
                    <h3 class="text-sm font-bold text-white uppercase tracking-wider flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 ring-4 ring-emerald-500/20"></span>
                        4. Property Photography & Media ({{ $property->images->count() }} attached)
                    </h3>
                    <p class="text-xs text-slate-400 mt-1">
                        Manage existing gallery photos, switch the primary cover photo, or upload additional high-resolution images.
                    </p>
                </div>
            </div>

            <!-- Existing Attached Photos -->
            @if ($property->images->isNotEmpty())
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-300">
                            Current Photos in Listing
                        </span>
                        <span class="text-2xs text-slate-400">Select cover radio or check delete box</span>
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                        @foreach ($property->images as $img)
                            <div class="relative rounded-xl overflow-hidden bg-slate-950 border transition-all duration-200 shadow-md flex flex-col {{ $img->is_cover ? 'border-emerald-500 ring-2 ring-emerald-500/40 shadow-emerald-950/50' : 'border-slate-800' }}">
                                <div class="aspect-16/9 w-full bg-slate-900 relative overflow-hidden">
                                    <img src="{{ $img->url }}" class="w-full h-full object-cover">

                                    @if ($img->is_cover)
                                        <span class="absolute top-2 left-2 px-2 py-0.5 rounded-md text-3xs font-extrabold uppercase tracking-wider bg-emerald-500 text-slate-950 shadow">
                                            ★ Cover
                                        </span>
                                    @endif
                                </div>

                                <div class="p-2.5 bg-slate-950/90 border-t border-slate-900 flex items-center justify-between text-2xs">
                                    <label class="flex items-center gap-1.5 text-slate-200 hover:text-white cursor-pointer select-none">
                                        <input type="radio" name="cover_image_id" value="{{ $img->id }}" {{ $img->is_cover ? 'checked' : '' }}
                                               class="rounded-full text-emerald-600 focus:ring-emerald-500 bg-slate-900 border-slate-700">
                                        <span class="font-medium {{ $img->is_cover ? 'text-emerald-400 font-bold' : '' }}">Cover</span>
                                    </label>

                                    <label class="flex items-center gap-1.5 text-rose-400 hover:text-rose-300 cursor-pointer select-none">
                                        <input type="checkbox" name="delete_images[]" value="{{ $img->id }}"
                                               class="rounded border-slate-700 bg-slate-900 text-rose-600 focus:ring-rose-500">
                                        <span class="text-3xs font-semibold">Delete</span>
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Additional Images Upload Section -->
            <div class="pt-4 border-t border-slate-800 space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-300">Upload Additional Images</span>
                        <p class="text-2xs text-slate-400 mt-0.5">Newly uploaded photos will be appended to the property gallery.</p>
                    </div>
                    <div class="flex items-center gap-2" x-show="newImagePreviews.length > 0">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-950/80 border border-emerald-800/80 text-emerald-400">
                            <span x-text="newImagePreviews.length + ' new photo' + (newImagePreviews.length > 1 ? 's' : '')"></span>
                            <span class="text-emerald-600">•</span>
                            <span x-text="totalNewPhotoSize"></span>
                        </span>
                        <button type="button" @click="clearAllNewImages()"
                                class="text-xs text-slate-400 hover:text-rose-400 transition-colors px-2 py-1 rounded-lg hover:bg-slate-800 cursor-pointer">
                            Clear
                        </button>
                    </div>
                </div>

                <!-- Hidden file input -->
                <input type="file" name="images[]" multiple accept="image/jpeg,image/png,image/webp" x-ref="newFileInput" @change="handleNewImageSelection($event)" class="hidden">

                <!-- Modern Interactive Dropzone -->
                <div @click="$refs.newFileInput.click()"
                     @dragover.prevent="isDraggingNew = true"
                     @dragleave.prevent="isDraggingNew = false"
                     @drop.prevent="handleNewDrop($event)"
                     class="relative group rounded-2xl border-2 border-dashed transition-all duration-200 p-6 sm:p-8 text-center cursor-pointer flex flex-col items-center justify-center gap-2.5"
                     :class="isDraggingNew ? 'border-emerald-400 bg-emerald-950/20 shadow-lg shadow-emerald-950/50' : 'border-slate-700/80 hover:border-emerald-500/60 bg-slate-950/40 hover:bg-slate-950/70'">

                    <div class="w-12 h-12 rounded-xl bg-slate-900 border border-slate-700 flex items-center justify-center text-emerald-400 group-hover:scale-105 group-hover:border-emerald-500/50 transition-all shadow-md">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 4v16m8-8H4" />
                        </svg>
                    </div>

                    <div>
                        <p class="text-xs sm:text-sm font-semibold text-white">
                            <span class="text-emerald-400 group-hover:underline underline-offset-4">Click to select new photos</span> or drag and drop here
                        </p>
                        <p class="text-2xs text-slate-400 mt-0.5">JPG, PNG, WebP up to 5MB each</p>
                    </div>
                </div>

                <!-- New Previews Grid -->
                <div x-show="newImagePreviews.length > 0" x-cloak class="space-y-3 pt-2">
                    <span class="text-xs font-semibold text-slate-300">Ready to Upload upon Saving:</span>

                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                        <template x-for="(item, idx) in newImagePreviews" :key="item.id">
                            <div class="relative group rounded-xl overflow-hidden bg-slate-950 border border-slate-800 shadow-md flex flex-col">
                                <div class="aspect-16/9 w-full bg-slate-900 relative overflow-hidden">
                                    <img :src="item.url" :alt="item.name" class="w-full h-full object-cover">
                                    <div class="absolute inset-0 bg-slate-950/60 backdrop-blur-xs opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                        <button type="button" @click="removeNewImage(idx)"
                                                title="Remove photo"
                                                class="w-8 h-8 rounded-lg bg-rose-950/80 hover:bg-rose-600 border border-rose-800 text-rose-300 hover:text-white flex items-center justify-center transition-all cursor-pointer shadow">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                                <div class="p-2 bg-slate-950/90 border-t border-slate-900 flex items-center justify-between text-2xs">
                                    <span class="text-white truncate max-w-[120px]" :title="item.name" x-text="item.name"></span>
                                    <span class="text-slate-400 font-mono text-3xs" x-text="item.size"></span>
                                </div>
                            </div>
                        </template>
                    </div>
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

