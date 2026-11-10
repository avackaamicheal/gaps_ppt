@extends('layouts.admin')

@section('title', 'Edit Customer: ' . $customer->name)
@section('header', 'Edit Customer Profile')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    <div>
        <a href="{{ route('admin.customers.show', $customer) }}" class="text-xs text-slate-400 hover:text-emerald-400 flex items-center gap-1 mb-2">
            &larr; Back to Customer Profile
        </a>
        <h2 class="text-2xl font-bold text-white tracking-tight">Edit: {{ $customer->name }}</h2>
        <p class="text-xs text-slate-400 mt-1">
            Update contact details or confidential notes for this client.
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

    <form method="POST" action="{{ route('admin.customers.update', $customer) }}" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="p-6 sm:p-8 rounded-2xl bg-slate-900 border border-slate-800 space-y-4 shadow-xl">
            <div>
                <label class="block text-2xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">
                    Customer / Client Name <span class="text-rose-400">*</span>
                </label>
                <input type="text" name="name" value="{{ old('name', $customer->name) }}" required
                       class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-xs text-white placeholder-slate-500 focus:outline-hidden focus:border-emerald-500">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-2xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">
                        Phone Number <span class="text-rose-400">*</span>
                    </label>
                    <input type="tel" name="phone" value="{{ old('phone', $customer->phone) }}" required
                           class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-xs text-white placeholder-slate-500 focus:outline-hidden focus:border-emerald-500">
                </div>

                <div>
                    <label class="block text-2xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">
                        Email Address <span class="text-slate-500 font-normal">(Optional)</span>
                    </label>
                    <input type="email" name="email" value="{{ old('email', $customer->email) }}"
                           class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-xs text-white placeholder-slate-500 focus:outline-hidden focus:border-emerald-500">
                </div>
            </div>

            <div>
                <label class="block text-2xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">
                    Primary Origin / Lead Source <span class="text-rose-400">*</span>
                </label>
                <select name="source" required
                        class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-xs text-white focus:outline-hidden focus:border-emerald-500">
                    @foreach (\App\Models\Customer::SOURCES as $code => $label)
                        <option value="{{ $code }}" {{ old('source', $customer->source) === $code ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-2xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">
                    Internal Client Notes & Profile Preferences
                </label>
                <textarea name="notes" rows="3"
                          class="w-full px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-700 text-xs text-white placeholder-slate-500 focus:outline-hidden focus:border-emerald-500">{{ old('notes', $customer->notes) }}</textarea>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('admin.customers.show', $customer) }}"
               class="px-5 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-slate-300 font-semibold text-xs border border-slate-800 transition-colors">
                Cancel
            </a>
            <button type="submit"
                    class="px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-lg shadow-emerald-950 transition-all cursor-pointer">
                Save Changes
            </button>
        </div>
    </form>

</div>
@endsection

