@extends('layouts.public')

@section('title', 'Victor Akande — Verified Land & Properties in Nigeria')

@section('content')
<!-- Hero Section -->
<section class="relative bg-slate-900 text-white overflow-hidden py-24 lg:py-32">
    <!-- Subtle background decoration -->
    <div class="absolute inset-0 opacity-20 bg-[radial-gradient(#10b981_1px,transparent_1px)] [background-size:24px_24px]"></div>
    <div class="absolute -top-40 -right-40 w-96 h-96 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl">
            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-xs font-semibold uppercase tracking-wider mb-6">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                Direct Independent Advisory • Zero Hidden Middlemen
            </div>

            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight text-white leading-tight mb-6">
                Secure Verified Land & Prime Properties Across Nigeria.
            </h1>

            <p class="text-lg sm:text-xl text-slate-300 leading-relaxed mb-8 max-w-2xl">
                Partner with an independent real estate professional who prioritizes thorough legal due diligence, authentic documentation, and transparent property acquisition.
            </p>

            <div class="flex flex-wrap items-center gap-4">
                <a href="#featured" class="px-6 py-3.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-semibold text-base shadow-lg shadow-emerald-600/30 transition-all flex items-center gap-2">
                    Browse Property Showcase
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </a>
                <a href="https://wa.me/2348000000000?text=Hello%20Victor,%20I%20would%20like%20to%20consult%20on%20available%20properties" target="_blank" rel="noopener noreferrer" class="px-6 py-3.5 rounded-xl bg-slate-800 hover:bg-slate-700 border border-slate-700 text-emerald-400 font-semibold text-base transition-all flex items-center gap-2">
                    <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
                        <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
                    </svg>
                    Direct WhatsApp Chat
                </a>
            </div>

            <!-- Key Trust Metrics -->
            <div class="mt-14 pt-10 border-t border-slate-800 grid grid-cols-2 sm:grid-cols-3 gap-6">
                <div>
                    <p class="text-3xl font-extrabold text-white">100%</p>
                    <p class="text-xs text-slate-400 mt-1 uppercase tracking-wider">Title Pre-Screening</p>
                </div>
                <div>
                    <p class="text-3xl font-extrabold text-white">Direct</p>
                    <p class="text-xs text-slate-400 mt-1 uppercase tracking-wider">Agent Access</p>
                </div>
                <div>
                    <p class="text-3xl font-extrabold text-white">Lagos & Beyond</p>
                    <p class="text-xs text-slate-400 mt-1 uppercase tracking-wider">Prime Growth Hubs</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Property Showcase / Introductory Section (Phase 1 Shell) -->
<section id="featured" class="py-20 bg-slate-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-12">
            <div>
                <span class="text-xs font-bold text-emerald-600 uppercase tracking-widest">Inventory Focus</span>
                <h2 class="text-3xl font-bold text-slate-900 mt-1">Land & Building Portfolio</h2>
                <p class="text-sm text-slate-500 mt-2 max-w-xl">
                    Discover prime opportunities currently in preparation. Full dynamic listings and location filters will be populated in Phase 2.
                </p>
            </div>
            <div class="mt-4 md:mt-0">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                    Phase 1 Initial Showcase
                </span>
            </div>
        </div>

        <!-- Category Showcase Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <!-- Land Card Placeholder -->
            <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-xs hover:shadow-md transition-shadow">
                <div class="h-48 bg-gradient-to-tr from-emerald-800 to-teal-700 flex items-center justify-center text-white relative">
                    <span class="absolute top-4 left-4 bg-emerald-950/80 text-emerald-300 text-xs font-bold px-2.5 py-1 rounded-md">
                        Category: Land
                    </span>
                    <div class="text-center p-6">
                        <svg class="w-12 h-12 mx-auto mb-2 text-emerald-300 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                        </svg>
                        <span class="text-lg font-bold">Residential & Commercial Plots</span>
                    </div>
                </div>
                <div class="p-6">
                    <h3 class="text-lg font-bold text-slate-900">Verified Plots & Acreages</h3>
                    <p class="text-sm text-slate-500 mt-2 leading-relaxed">
                        Excision, Gazette, Certificate of Occupancy, and Governor's Consent land parcels across Ibeju-Lekki, Epe, and Abuja growth corridors.
                    </p>
                    <div class="mt-6 pt-4 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                        <span>Includes Survey & Title Check</span>
                        <a href="https://wa.me/2348000000000?text=I%20am%20enquiring%20about%20land%20listings" target="_blank" class="text-emerald-600 font-semibold hover:text-emerald-700">Enquire Now &rarr;</a>
                    </div>
                </div>
            </div>

            <!-- Residential Buildings Card Placeholder -->
            <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-xs hover:shadow-md transition-shadow">
                <div class="h-48 bg-gradient-to-tr from-slate-800 to-slate-700 flex items-center justify-center text-white relative">
                    <span class="absolute top-4 left-4 bg-slate-900/80 text-slate-200 text-xs font-bold px-2.5 py-1 rounded-md">
                        Category: Buildings
                    </span>
                    <div class="text-center p-6">
                        <svg class="w-12 h-12 mx-auto mb-2 text-slate-300 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        <span class="text-lg font-bold">Residential Homes & Duplexes</span>
                    </div>
                </div>
                <div class="p-6">
                    <h3 class="text-lg font-bold text-slate-900">Modern Living & Estates</h3>
                    <p class="text-sm text-slate-500 mt-2 leading-relaxed">
                        Detached duplexes, terrace houses, and luxury apartments in secure gated communities in Lekki, Ikoyi, Ikeja GRA, and Guzape.
                    </p>
                    <div class="mt-6 pt-4 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                        <span>Quality Structural Inspection</span>
                        <a href="https://wa.me/2348000000000?text=I%20am%20enquiring%20about%20residential%20buildings" target="_blank" class="text-emerald-600 font-semibold hover:text-emerald-700">Enquire Now &rarr;</a>
                    </div>
                </div>
            </div>

            <!-- Commercial Card Placeholder -->
            <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-xs hover:shadow-md transition-shadow">
                <div class="h-48 bg-gradient-to-tr from-cyan-900 to-blue-800 flex items-center justify-center text-white relative">
                    <span class="absolute top-4 left-4 bg-cyan-950/80 text-cyan-200 text-xs font-bold px-2.5 py-1 rounded-md">
                        Category: Commercial
                    </span>
                    <div class="text-center p-6">
                        <svg class="w-12 h-12 mx-auto mb-2 text-cyan-300 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        <span class="text-lg font-bold">Office & Retail Spaces</span>
                    </div>
                </div>
                <div class="p-6">
                    <h3 class="text-lg font-bold text-slate-900">Commercial Real Estate</h3>
                    <p class="text-sm text-slate-500 mt-2 leading-relaxed">
                        Strategic commercial land and commercial buildings along major business arteries with strong rental yields and capital appreciation.
                    </p>
                    <div class="mt-6 pt-4 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                        <span>High ROI Investment</span>
                        <a href="https://wa.me/2348000000000?text=I%20am%20enquiring%20about%20commercial%20spaces" target="_blank" class="text-emerald-600 font-semibold hover:text-emerald-700">Enquire Now &rarr;</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- About the Agent Section -->
<section id="about" class="py-20 bg-white border-t border-slate-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
            <div class="lg:col-span-5">
                <div class="relative">
                    <div class="w-full aspect-4/5 rounded-2xl bg-gradient-to-tr from-slate-800 to-slate-700 flex flex-col justify-end p-8 text-white shadow-xl overflow-hidden">
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-900/40 to-transparent"></div>
                        <div class="relative z-10">
                            <span class="px-3 py-1 rounded-full bg-emerald-500/20 border border-emerald-400/40 text-emerald-300 text-xs font-semibold uppercase tracking-wider">
                                Independent Advisor
                            </span>
                            <h3 class="text-2xl font-bold mt-2">Victor Akande</h3>
                            <p class="text-sm text-slate-300 mt-1">Real Estate Investment & Property Advisory</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-7 space-y-6">
                <div>
                    <span class="text-xs font-bold text-emerald-600 uppercase tracking-widest">Why Work Directly With An Independent Agent</span>
                    <h2 class="text-3xl font-bold text-slate-900 mt-1">Personal Integrity, Thorough Due Diligence</h2>
                </div>

                <p class="text-slate-600 leading-relaxed">
                    Navigating property acquisition in Nigeria requires transparency, title validation, and direct communication. As an independent real estate agent, I offer personalized guidance without multi-layered agency bureaucracy.
                </p>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200">
                        <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold mb-3">
                            ✓
                        </div>
                        <h4 class="font-bold text-slate-900 text-sm">Verified Documentation</h4>
                        <p class="text-xs text-slate-500 mt-1">Every property is inspected against official land records and survey plans.</p>
                    </div>

                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200">
                        <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold mb-3">
                            ✓
                        </div>
                        <h4 class="font-bold text-slate-900 text-sm">Direct Seller Access</h4>
                        <p class="text-xs text-slate-500 mt-1">No middlemen chains; straightforward price and terms negotiation.</p>
                    </div>

                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200">
                        <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold mb-3">
                            ✓
                        </div>
                        <h4 class="font-bold text-slate-900 text-sm">Physical Site Inspections</h4>
                        <p class="text-xs text-slate-500 mt-1">Coordinated site visits with prompt meeting point arrangements.</p>
                    </div>

                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200">
                        <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold mb-3">
                            ✓
                        </div>
                        <h4 class="font-bold text-slate-900 text-sm">Post-Acquisition Support</h4>
                        <p class="text-xs text-slate-500 mt-1">Guidance on perfection of titles and site possession security.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Direct Contact Section -->
<section id="contact" class="py-20 bg-slate-900 text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-gradient-to-r from-emerald-900/60 to-slate-800 rounded-3xl p-8 sm:p-12 border border-emerald-500/20 shadow-2xl">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-center">
                <div>
                    <span class="text-xs font-bold text-emerald-400 uppercase tracking-widest">Get In Touch</span>
                    <h2 class="text-3xl sm:text-4xl font-extrabold mt-1 text-white">Ready to Inspect or Enquire?</h2>
                    <p class="text-slate-300 mt-3 text-base leading-relaxed">
                        Call or send a WhatsApp message today for instant responses regarding verified lands and buildings currently available.
                    </p>

                    <div class="mt-8 space-y-3 text-sm">
                        <div class="flex items-center gap-3 text-slate-200">
                            <span class="w-8 h-8 rounded-lg bg-emerald-500/20 text-emerald-400 flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                            </span>
                            <span>Phone: +234 800 000 0000</span>
                        </div>
                        <div class="flex items-center gap-3 text-slate-200">
                            <span class="w-8 h-8 rounded-lg bg-emerald-500/20 text-emerald-400 flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
                                    <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
                                </svg>
                            </span>
                            <span>WhatsApp: +234 800 000 0000</span>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row gap-4 lg:justify-end">
                    <a href="https://wa.me/2348000000000?text=Hello%20Victor,%20I%20am%20interested%20in%20arranging%20an%20inspection" target="_blank" rel="noopener noreferrer" class="px-8 py-4 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-center text-base shadow-xl shadow-emerald-950 transition-all">
                        Chat on WhatsApp
                    </a>
                    <a href="tel:+2348000000000" class="px-8 py-4 rounded-xl bg-white hover:bg-slate-100 text-slate-900 font-bold text-center text-base transition-all">
                        Direct Phone Call
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
