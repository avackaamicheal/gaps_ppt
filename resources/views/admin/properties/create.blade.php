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

        <!-- 4. Media: Property Images -->
        <div class="p-6 rounded-2xl bg-slate-900 border border-slate-800 space-y-4">
            <h3 class="text-sm font-bold text-white uppercase tracking-wider pb-3 border-b border-slate-800 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                4. Property Images & Photography
            </h3>

            <p class="text-xs text-slate-400">
                Upload one or multiple photos (JPG, PNG, WebP up to 5MB each). The first photo will serve as the cover image by default.
            </p>

            <div class="p-6 rounded-2xl border-2 border-dashed border-slate-700 bg-slate-950/60 text-center hover:border-emerald-500 transition-colors">
                <input type="file" name="images[]" multiple accept="image/*" class="w-full text-xs text-slate-400 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-emerald-600 file:text-white hover:file:bg-emerald-500 cursor-pointer">
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
