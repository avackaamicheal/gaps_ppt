@extends('layouts.admin')

@section('title', 'Edit Client Testimonial')
@section('header', 'Edit Client Testimonial')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- Header / Back Link -->
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.testimonials.index') }}" class="inline-flex items-center gap-2 text-xs font-semibold text-slate-400 hover:text-white transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back to Testimonials
        </a>

        <span class="text-2xs font-mono text-slate-500">ID: #{{ $testimonial->id }}</span>
    </div>

    <!-- Form Container -->
    <div class="p-6 sm:p-8 rounded-3xl bg-slate-900 border border-slate-800 shadow-2xl">
        <div class="border-b border-slate-800 pb-5 mb-6">
            <h2 class="text-xl font-bold text-white tracking-tight">Edit Client Testimonial</h2>
            <p class="text-xs text-slate-400 mt-1">
                Modify review details from {{ $testimonial->client_name }}.
            </p>
        </div>

        <form method="POST" action="{{ route('admin.testimonials.update', $testimonial) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Client Identity Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <!-- Client Name -->
                <div>
                    <label for="client_name" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">
                        Client Full Name <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" id="client_name" name="client_name" value="{{ old('client_name', $testimonial->client_name) }}" required
                           class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-800 text-sm text-white placeholder-slate-600 focus:outline-hidden focus:border-emerald-500 transition-colors @error('client_name') border-rose-500 @enderror">
                    @error('client_name')
                        <p class="text-rose-400 text-2xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Role / Title -->
                <div>
                    <label for="client_role_or_title" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">
                        Client Designation / Subtitle
                    </label>
                    <input type="text" id="client_role_or_title" name="client_role_or_title" value="{{ old('client_role_or_title', $testimonial->client_role_or_title) }}"
                           class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-800 text-sm text-white placeholder-slate-600 focus:outline-hidden focus:border-emerald-500 transition-colors @error('client_role_or_title') border-rose-500 @enderror">
                    @error('client_role_or_title')
                        <p class="text-rose-400 text-2xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                <!-- Location -->
                <div>
                    <label for="client_location" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">
                        Client Location / City
                    </label>
                    <input type="text" id="client_location" name="client_location" value="{{ old('client_location', $testimonial->client_location) }}"
                           class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-800 text-sm text-white placeholder-slate-600 focus:outline-hidden focus:border-emerald-500 transition-colors @error('client_location') border-rose-500 @enderror">
                    @error('client_location')
                        <p class="text-rose-400 text-2xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Star Rating -->
                <div>
                    <label for="rating" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">
                        Rating (Stars) <span class="text-rose-500">*</span>
                    </label>
                    <select id="rating" name="rating" required
                            class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-800 text-sm text-white focus:outline-hidden focus:border-emerald-500 transition-colors @error('rating') border-rose-500 @enderror">
                        <option value="5" {{ old('rating', (string)$testimonial->rating) == '5' ? 'selected' : '' }}>★★★★★ (5 Stars - Exceptional)</option>
                        <option value="4" {{ old('rating', (string)$testimonial->rating) == '4' ? 'selected' : '' }}>★★★★☆ (4 Stars - Very Good)</option>
                        <option value="3" {{ old('rating', (string)$testimonial->rating) == '3' ? 'selected' : '' }}>★★★☆☆ (3 Stars - Good)</option>
                        <option value="2" {{ old('rating', (string)$testimonial->rating) == '2' ? 'selected' : '' }}>★★☆☆☆ (2 Stars - Fair)</option>
                        <option value="1" {{ old('rating', (string)$testimonial->rating) == '1' ? 'selected' : '' }}>★☆☆☆☆ (1 Star - Poor)</option>
                    </select>
                    @error('rating')
                        <p class="text-rose-400 text-2xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Associated Property -->
                <div>
                    <label for="property_id" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">
                        Linked Property (Optional)
                    </label>
                    <select id="property_id" name="property_id"
                            class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-800 text-sm text-white focus:outline-hidden focus:border-emerald-500 transition-colors @error('property_id') border-rose-500 @enderror">
                        <option value="">-- General Agency Experience --</option>
                        @foreach ($properties as $property)
                            <option value="{{ $property->id }}" {{ old('property_id', $testimonial->property_id) == $property->id ? 'selected' : '' }}>
                                {{ $property->reference }} - {{ Str::limit($property->title, 35) }}
                            </option>
                        @endforeach
                    </select>
                    @error('property_id')
                        <p class="text-rose-400 text-2xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Review Content / Quote -->
            <div>
                <label for="content" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">
                    Testimonial / Review Content <span class="text-rose-500">*</span>
                </label>
                <textarea id="content" name="content" rows="4" required
                          class="w-full px-4 py-3 rounded-xl bg-slate-950 border border-slate-800 text-sm text-white placeholder-slate-600 focus:outline-hidden focus:border-emerald-500 transition-colors leading-relaxed @error('content') border-rose-500 @enderror">{{ old('content', $testimonial->content) }}</textarea>
                @error('content')
                    <p class="text-rose-400 text-2xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Sort Order and Publishing Checkbox -->
            <div class="p-4 rounded-2xl bg-slate-950/60 border border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <input type="checkbox" id="is_published" name="is_published" value="1"
                           {{ old('is_published', $testimonial->is_published) ? 'checked' : '' }}
                           class="w-4 h-4 rounded border-slate-700 bg-slate-900 text-emerald-500 focus:ring-emerald-500 focus:ring-offset-slate-900 cursor-pointer">
                    <label for="is_published" class="text-xs font-semibold text-slate-200 cursor-pointer select-none">
                        Publish on Public Website
                        <span class="block text-2xs font-normal text-slate-400">Make this review visible on the public homepage immediately</span>
                    </label>
                </div>

                <div class="flex items-center gap-2">
                    <label for="sort_order" class="text-xs text-slate-400 font-semibold whitespace-nowrap">Sort Priority:</label>
                    <input type="number" id="sort_order" name="sort_order" value="{{ old('sort_order', $testimonial->sort_order) }}" min="0" max="999"
                           class="w-20 px-3 py-1.5 rounded-lg bg-slate-900 border border-slate-800 text-xs font-mono text-white text-center focus:outline-hidden focus:border-emerald-500">
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-800">
                <a href="{{ route('admin.testimonials.index') }}"
                   class="px-5 py-2.5 rounded-xl border border-slate-700 text-slate-300 hover:text-white hover:bg-slate-800 text-xs font-bold transition-colors">
                    Cancel
                </a>
                <button type="submit"
                        class="px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold shadow-lg shadow-emerald-950 transition-all cursor-pointer">
                    Update Testimonial
                </button>
            </div>
        </form>
    </div>

</div>
@endsection

