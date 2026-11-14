@extends('layouts.admin')

@section('title', 'Website Settings & Agent Branding')
@section('header', 'Website Settings')

@section('content')
<div class="max-w-5xl mx-auto space-y-6" x-data="{ activeTab: 'brand' }">

    <!-- Header Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-white tracking-tight">Website Settings & Agent Branding</h2>
            <p class="text-xs text-slate-400 mt-1">
                Customize your agency brand, contact phone numbers, WhatsApp links, and homepage copy without code changes.
            </p>
        </div>

        <div class="flex items-center gap-2">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-2xs font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                Dynamic Cache Active
            </span>
        </div>
    </div>

    <!-- Tabbed Settings Card -->
    <div class="bg-slate-900 border border-slate-800 rounded-3xl overflow-hidden shadow-2xl">

        <!-- Navigation Tabs -->
        <div class="flex items-center gap-2 px-6 pt-5 pb-0 border-b border-slate-800 overflow-x-auto">
            <button type="button" @click="activeTab = 'brand'"
                    :class="activeTab === 'brand' ? 'border-emerald-500 text-emerald-400' : 'border-transparent text-slate-400 hover:text-white'"
                    class="pb-3 px-3 border-b-2 font-bold text-xs transition-colors flex items-center gap-2 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
                Brand & Profile
            </button>

            <button type="button" @click="activeTab = 'contact'"
                    :class="activeTab === 'contact' ? 'border-emerald-500 text-emerald-400' : 'border-transparent text-slate-400 hover:text-white'"
                    class="pb-3 px-3 border-b-2 font-bold text-xs transition-colors flex items-center gap-2 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                </svg>
                Contact & Channels
            </button>

            <button type="button" @click="activeTab = 'content'"
                    :class="activeTab === 'content' ? 'border-emerald-500 text-emerald-400' : 'border-transparent text-slate-400 hover:text-white'"
                    class="pb-3 px-3 border-b-2 font-bold text-xs transition-colors flex items-center gap-2 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7" />
                </svg>
                Homepage Copy
            </button>

            <button type="button" @click="activeTab = 'social'"
                    :class="activeTab === 'social' ? 'border-emerald-500 text-emerald-400' : 'border-transparent text-slate-400 hover:text-white'"
                    class="pb-3 px-3 border-b-2 font-bold text-xs transition-colors flex items-center gap-2 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                </svg>
                Social Media
            </button>

            <button type="button" @click="activeTab = 'seo'"
                    :class="activeTab === 'seo' ? 'border-emerald-500 text-emerald-400' : 'border-transparent text-slate-400 hover:text-white'"
                    class="pb-3 px-3 border-b-2 font-bold text-xs transition-colors flex items-center gap-2 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                SEO & Meta
            </button>
        </div>

        <!-- Form -->
        <form method="POST" action="{{ route('admin.settings.update') }}" class="p-6 sm:p-8 space-y-6">
            @csrf
            @method('PUT')

            <!-- Tab 1: Brand & Profile -->
            <div x-show="activeTab === 'brand'" class="space-y-6">
                <div>
                    <h3 class="text-sm font-bold text-white uppercase tracking-wider mb-1">Agent & Agency Identity</h3>
                    <p class="text-2xs text-slate-400">These details display in the public site header, footer, and inquiry credentials.</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label for="agency_name" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">
                            Agency Business Name <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" id="agency_name" name="agency_name" value="{{ old('agency_name', $settings['agency_name'] ?? 'Victor Akande Real Estate') }}" required
                               class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-800 text-sm text-white focus:outline-hidden focus:border-emerald-500 transition-colors">
                        @error('agency_name')
                            <p class="text-rose-400 text-2xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="agent_name" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">
                            Principal Agent Full Name <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" id="agent_name" name="agent_name" value="{{ old('agent_name', $settings['agent_name'] ?? 'Victor Akande') }}" required
                               class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-800 text-sm text-white focus:outline-hidden focus:border-emerald-500 transition-colors">
                        @error('agent_name')
                            <p class="text-rose-400 text-2xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="agent_title" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">
                        Professional Advisory Title
                    </label>
                    <input type="text" id="agent_title" name="agent_title" value="{{ old('agent_title', $settings['agent_title'] ?? 'Lead Independent Real Estate Advisor') }}"
                           class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-800 text-sm text-white focus:outline-hidden focus:border-emerald-500 transition-colors"
                           placeholder="e.g. Lead Independent Real Estate Advisor">
                    @error('agent_title')
                        <p class="text-rose-400 text-2xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Tab 2: Contact & Channels -->
            <div x-show="activeTab === 'contact'" x-cloak class="space-y-6">
                <div>
                    <h3 class="text-sm font-bold text-white uppercase tracking-wider mb-1">Direct Contact Channels</h3>
                    <p class="text-2xs text-slate-400">Used for WhatsApp click-to-chat links, direct phone calls, and email contact info.</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label for="phone" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">
                            Direct Telephone Number <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" id="phone" name="phone" value="{{ old('phone', $settings['phone'] ?? '+234 800 000 0000') }}" required
                               class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-800 text-sm text-white focus:outline-hidden focus:border-emerald-500 transition-colors"
                               placeholder="+234 802 345 6789">
                        @error('phone')
                            <p class="text-rose-400 text-2xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="whatsapp_number" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">
                            WhatsApp Phone (Digits Only) <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" id="whatsapp_number" name="whatsapp_number" value="{{ old('whatsapp_number', $settings['whatsapp_number'] ?? '2348000000000') }}" required
                               class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-800 text-sm text-white focus:outline-hidden focus:border-emerald-500 transition-colors"
                               placeholder="e.g. 2348023456789 (including country code)">
                        <p class="text-3xs text-slate-500 mt-1">Formatted for wa.me links with Nigeria country prefix (234).</p>
                        @error('whatsapp_number')
                            <p class="text-rose-400 text-2xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">
                            Direct Enquiry Email <span class="text-rose-500">*</span>
                        </label>
                        <input type="email" id="email" name="email" value="{{ old('email', $settings['email'] ?? 'victor@example.com') }}" required
                               class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-800 text-sm text-white focus:outline-hidden focus:border-emerald-500 transition-colors"
                               placeholder="victor@example.com">
                        @error('email')
                            <p class="text-rose-400 text-2xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="office_address" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">
                            Office / Consultation Address
                        </label>
                        <input type="text" id="office_address" name="office_address" value="{{ old('office_address', $settings['office_address'] ?? 'Lekki Phase 1, Lagos State, Nigeria') }}"
                               class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-800 text-sm text-white focus:outline-hidden focus:border-emerald-500 transition-colors"
                               placeholder="e.g. Lekki Phase 1, Lagos, Nigeria">
                        @error('office_address')
                            <p class="text-rose-400 text-2xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Tab 3: Homepage Copy -->
            <div x-show="activeTab === 'content'" x-cloak class="space-y-6">
                <div>
                    <h3 class="text-sm font-bold text-white uppercase tracking-wider mb-1">Homepage Headlines & Narrative</h3>
                    <p class="text-2xs text-slate-400">Control the hero proposition and personal background narrative displayed on the homepage.</p>
                </div>

                <div>
                    <label for="homepage_headline" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">
                        Hero Main Headline
                    </label>
                    <input type="text" id="homepage_headline" name="homepage_headline"
                           value="{{ old('homepage_headline', $settings['homepage_headline'] ?? 'Secure Verified Land & Prime Properties Across Nigeria.') }}"
                           class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-800 text-sm text-white focus:outline-hidden focus:border-emerald-500 transition-colors">
                    @error('homepage_headline')
                        <p class="text-rose-400 text-2xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="homepage_subheadline" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">
                        Hero Sub-Headline Description
                    </label>
                    <textarea id="homepage_subheadline" name="homepage_subheadline" rows="2"
                              class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-800 text-sm text-white focus:outline-hidden focus:border-emerald-500 transition-colors leading-relaxed">{{ old('homepage_subheadline', $settings['homepage_subheadline'] ?? 'Partner with an independent real estate professional who prioritizes thorough legal due diligence, authentic documentation, and transparent property acquisition.') }}</textarea>
                    @error('homepage_subheadline')
                        <p class="text-rose-400 text-2xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="about_narrative" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">
                        About Agent Narrative (Bio / Value Proposition)
                    </label>
                    <textarea id="about_narrative" name="about_narrative" rows="4"
                              class="w-full px-4 py-3 rounded-xl bg-slate-950 border border-slate-800 text-sm text-white focus:outline-hidden focus:border-emerald-500 transition-colors leading-relaxed">{{ old('about_narrative', $settings['about_narrative'] ?? 'Navigating property acquisition in Nigeria requires transparency, title validation, and direct communication. As an independent real estate agent, I offer personalized guidance without multi-layered agency bureaucracy.') }}</textarea>
                    @error('about_narrative')
                        <p class="text-rose-400 text-2xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Tab 4: Social Media -->
            <div x-show="activeTab === 'social'" x-cloak class="space-y-6">
                <div>
                    <h3 class="text-sm font-bold text-white uppercase tracking-wider mb-1">Social Media & Channel Links</h3>
                    <p class="text-2xs text-slate-400">Optional social profile handles or URLs for client engagement.</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label for="social_instagram" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">
                            Instagram Profile URL
                        </label>
                        <input type="text" id="social_instagram" name="social_instagram" value="{{ old('social_instagram', $settings['social_instagram'] ?? '') }}"
                               class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-800 text-sm text-white focus:outline-hidden focus:border-emerald-500 transition-colors"
                               placeholder="https://instagram.com/victorakande_realty">
                    </div>

                    <div>
                        <label for="social_linkedin" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">
                            LinkedIn Profile URL
                        </label>
                        <input type="text" id="social_linkedin" name="social_linkedin" value="{{ old('social_linkedin', $settings['social_linkedin'] ?? '') }}"
                               class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-800 text-sm text-white focus:outline-hidden focus:border-emerald-500 transition-colors"
                               placeholder="https://linkedin.com/in/victorakande">
                    </div>

                    <div>
                        <label for="social_facebook" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">
                            Facebook Page URL
                        </label>
                        <input type="text" id="social_facebook" name="social_facebook" value="{{ old('social_facebook', $settings['social_facebook'] ?? '') }}"
                               class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-800 text-sm text-white focus:outline-hidden focus:border-emerald-500 transition-colors"
                               placeholder="https://facebook.com/victorakanderealestate">
                    </div>

                    <div>
                        <label for="social_twitter" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">
                            X (Twitter) Profile URL
                        </label>
                        <input type="text" id="social_twitter" name="social_twitter" value="{{ old('social_twitter', $settings['social_twitter'] ?? '') }}"
                               class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-800 text-sm text-white focus:outline-hidden focus:border-emerald-500 transition-colors"
                               placeholder="https://x.com/victorakande">
                    </div>
                </div>
            </div>

            <!-- Tab 5: SEO & Search -->
            <div x-show="activeTab === 'seo'" x-cloak class="space-y-6">
                <div>
                    <h3 class="text-sm font-bold text-white uppercase tracking-wider mb-1">Search Engine Optimization (SEO)</h3>
                    <p class="text-2xs text-slate-400">Default metadata used for search engine indexing and social link sharing previews.</p>
                </div>

                <div>
                    <label for="meta_title" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">
                        Default Meta Title
                    </label>
                    <input type="text" id="meta_title" name="meta_title" value="{{ old('meta_title', $settings['meta_title'] ?? 'Victor Akande Real Estate — Verified Nigerian Properties & Land') }}"
                           class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-800 text-sm text-white focus:outline-hidden focus:border-emerald-500 transition-colors">
                    @error('meta_title')
                        <p class="text-rose-400 text-2xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="meta_description" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2">
                        Default Meta Description
                    </label>
                    <textarea id="meta_description" name="meta_description" rows="3"
                              class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-800 text-sm text-white focus:outline-hidden focus:border-emerald-500 transition-colors leading-relaxed">{{ old('meta_description', $settings['meta_description'] ?? 'Secure verified land and prime residential and commercial properties across Nigeria with Victor Akande.') }}</textarea>
                    @error('meta_description')
                        <p class="text-rose-400 text-2xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-between pt-6 border-t border-slate-800">
                <span class="text-2xs text-slate-500">All changes update cache and live pages instantly.</span>

                <button type="submit"
                        class="px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold shadow-lg shadow-emerald-950 transition-all cursor-pointer">
                    Save Website Settings
                </button>
            </div>
        </form>

    </div>

</div>
@endsection

