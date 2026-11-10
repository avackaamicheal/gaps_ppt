@extends('layouts.admin')

@section('title', 'Record Offline Lead')
@section('header', 'Capture Inbound Lead')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <div>
        <a href="{{ route('admin.enquiries.index') }}" class="text-xs text-slate-400 hover:text-emerald-400 flex items-center gap-1 mb-2">
            &larr; Back to Enquiries
        </a>
        <h2 class="text-2xl font-bold text-white tracking-tight">Record Offline Lead</h2>
        <p class="text-xs text-slate-400 mt-1">
            Log enquiries received directly through phone conversations, WhatsApp chats, or client referrals. Existing customer records will be automatically deduplicated.
        </p>
    </div>

    @if ($errors->any())
        <div class="p-4 rounded-2xl bg-rose-950/60 border border-rose-800 text-rose-300 text-xs space-y-1">
            <p class="font-bold">Please correct the following errors:</p>
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.enquiries.store') }}" class="space-y-6">
        @csrf

        <div class="p-6 sm:p-8 rounded-2xl bg-slate-900 border border-slate-800 space-y-6 shadow-xl">
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 pb-3 border-b border-slate-800 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                Prospect Contact Information
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-2xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">
                        Client / Prospect Name <span class="text-rose-400">*</span>
                    </label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                           placeholder="e.g. Alh. Sanusi Dantata"
                           class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-xs text-white placeholder-slate-500 focus:outline-hidden focus:border-emerald-500">
                </div>

                <div>
                    <label class="block text-2xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">
                        Phone Number <span class="text-rose-400">*</span>
                    </label>
                    <input type="tel" name="phone" value="{{ old('phone') }}" required
                           placeholder="e.g. 0803 555 1234"
                           class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-xs text-white placeholder-slate-500 focus:outline-hidden focus:border-emerald-500">
                </div>

                <div>
                    <label class="block text-2xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">
                        Email Address <span class="text-slate-500 font-normal">(Optional)</span>
                    </label>
                    <input type="email" name="email" value="{{ old('email') }}"
                           placeholder="e.g. client@example.com"
                           class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-xs text-white placeholder-slate-500 focus:outline-hidden focus:border-emerald-500">
                </div>

                <div>
                    <label class="block text-2xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">
                        Lead Source Channel <span class="text-rose-400">*</span>
                    </label>
                    <select name="source" required
                            class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-xs text-white focus:outline-hidden focus:border-emerald-500">
                        @foreach (\App\Models\Enquiry::SOURCES as $code => $label)
                            <option value="{{ $code }}" {{ old('source', 'whatsapp') === $code ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 pt-4 pb-3 border-b border-slate-800 flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                Lead Details & Property Interest
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-2xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">
                        Referenced Property <span class="text-slate-500 font-normal">(Optional)</span>
                    </label>
                    <select name="property_id"
                            class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-xs text-white focus:outline-hidden focus:border-emerald-500">
                        <option value="">General Inquiry / Unassigned</option>
                        @foreach ($properties as $prop)
                            <option value="{{ $prop->id }}" {{ old('property_id') == $prop->id ? 'selected' : '' }}>
                                [{{ $prop->reference }}] {{ $prop->title }} ({{ $prop->formatted_price }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-2xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">
                        Initial Lead Status <span class="text-rose-400">*</span>
                    </label>
                    <select name="status" required
                            class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-xs text-white focus:outline-hidden focus:border-emerald-500">
                        @foreach (\App\Models\Enquiry::STATUSES as $code => $label)
                            <option value="{{ $code }}" {{ old('status', 'contacted') === $code ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-2xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">
                    Lead Inquiry / What did they ask for? <span class="text-rose-400">*</span>
                </label>
                <textarea name="message" rows="3" required
                          placeholder="Summary of conversation, questions asked, budget, or preferred location..."
                          class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-xs text-white placeholder-slate-500 focus:outline-hidden focus:border-emerald-500">{{ old('message') }}</textarea>
            </div>

            <div>
                <label class="block text-2xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">
                    Victor's Internal Notes / Next Action <span class="text-slate-500 font-normal">(Confidential)</span>
                </label>
                <textarea name="internal_notes" rows="2"
                          placeholder="e.g. Requested inspection for Saturday 11am; Follow up after C of O verification..."
                          class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-xs text-white placeholder-slate-500 focus:outline-hidden focus:border-emerald-500">{{ old('internal_notes') }}</textarea>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('admin.enquiries.index') }}"
               class="px-5 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-slate-300 font-semibold text-xs border border-slate-800 transition-colors">
                Cancel
            </a>
            <button type="submit"
                    class="px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-lg shadow-emerald-950 transition-all cursor-pointer">
                Save Lead & Update Customer CRM
            </button>
        </div>
    </form>

</div>
@endsection

