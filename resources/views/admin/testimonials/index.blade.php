@extends('layouts.admin')

@section('title', 'Client Testimonials & Trust Endorsements')
@section('header', 'Client Testimonials')

@section('content')
<div class="space-y-6">

    <!-- Header Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-white tracking-tight">Client Testimonials & Endorsements</h2>
            <p class="text-xs text-slate-400 mt-1">
                Manage verified buyer reviews and client feedback displayed on the public homepage.
            </p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('admin.testimonials.create') }}"
               class="px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-lg shadow-emerald-950 transition-all flex items-center gap-2 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Add Client Testimonial
            </a>
        </div>
    </div>

    <!-- Status Tabs -->
    <div class="flex items-center gap-2 overflow-x-auto pb-2 border-b border-slate-800 text-xs font-semibold">
        <a href="{{ route('admin.testimonials.index') }}"
           class="px-3.5 py-1.5 rounded-lg transition-colors flex items-center gap-2 {{ !$status ? 'bg-emerald-600 text-white' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">
            <span>All Testimonials</span>
            <span class="px-1.5 py-0.2 rounded-full text-3xs font-mono {{ !$status ? 'bg-emerald-800 text-white' : 'bg-slate-800 text-slate-400' }}">{{ $counts['total'] }}</span>
        </a>

        <a href="{{ route('admin.testimonials.index', ['status' => 'published']) }}"
           class="px-3.5 py-1.5 rounded-lg transition-colors flex items-center gap-2 {{ $status === 'published' ? 'bg-emerald-600 text-white' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">
            <span>Published</span>
            <span class="px-1.5 py-0.2 rounded-full text-3xs font-mono {{ $status === 'published' ? 'bg-emerald-800 text-white' : 'bg-slate-800 text-slate-400' }}">{{ $counts['published'] }}</span>
        </a>

        <a href="{{ route('admin.testimonials.index', ['status' => 'unpublished']) }}"
           class="px-3.5 py-1.5 rounded-lg transition-colors flex items-center gap-2 {{ $status === 'unpublished' ? 'bg-emerald-600 text-white' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">
            <span>Hidden / Draft</span>
            <span class="px-1.5 py-0.2 rounded-full text-3xs font-mono {{ $status === 'unpublished' ? 'bg-emerald-800 text-white' : 'bg-slate-800 text-slate-400' }}">{{ $counts['unpublished'] }}</span>
        </a>
    </div>

    <!-- Testimonials Table Card -->
    <div class="bg-slate-900 border border-slate-800 rounded-2xl overflow-hidden shadow-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-300">
                <thead class="bg-slate-950/60 text-3xs uppercase tracking-wider text-slate-400 border-b border-slate-800">
                    <tr>
                        <th class="py-3.5 px-4 font-bold">Client & Identity</th>
                        <th class="py-3.5 px-4 font-bold">Rating</th>
                        <th class="py-3.5 px-4 font-bold">Review Quote</th>
                        <th class="py-3.5 px-4 font-bold">Linked Property</th>
                        <th class="py-3.5 px-4 font-bold">Visibility</th>
                        <th class="py-3.5 px-4 font-bold text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60 font-medium">
                    @forelse ($testimonials as $testimonial)
                        <tr class="hover:bg-slate-800/30 transition-colors">
                            <!-- Client -->
                            <td class="py-4 px-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full bg-emerald-600/20 text-emerald-400 border border-emerald-500/30 flex items-center justify-center font-bold text-xs shrink-0">
                                        {{ strtoupper(substr($testimonial->client_name, 0, 1)) }}
                                    </div>
                                    <div class="min-w-0">
                                        <p class="font-bold text-white text-sm truncate">{{ $testimonial->client_name }}</p>
                                        <p class="text-2xs text-slate-400 truncate">
                                            {{ $testimonial->client_role_or_title ?? 'Verified Buyer' }}
                                            @if ($testimonial->client_location)
                                                • <span class="text-slate-500">{{ $testimonial->client_location }}</span>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </td>

                            <!-- Rating -->
                            <td class="py-4 px-4 whitespace-nowrap">
                                <div class="flex items-center gap-1 text-amber-400 text-sm">
                                    @for ($i = 1; $i <= 5; $i++)
                                        @if ($i <= $testimonial->rating)
                                            <span>★</span>
                                        @else
                                            <span class="text-slate-700">★</span>
                                        @endif
                                    @endfor
                                    <span class="text-2xs font-mono font-bold text-slate-400 ml-1">({{ $testimonial->rating }}.0)</span>
                                </div>
                            </td>

                            <!-- Content -->
                            <td class="py-4 px-4 max-w-md">
                                <p class="text-slate-300 text-xs line-clamp-2 italic">
                                    "{{ $testimonial->content }}"
                                </p>
                            </td>

                            <!-- Property -->
                            <td class="py-4 px-4 whitespace-nowrap">
                                @if ($testimonial->property)
                                    <a href="{{ route('admin.properties.show', $testimonial->property) }}"
                                       class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md bg-slate-800 text-emerald-400 border border-slate-700 hover:border-emerald-500/50 text-2xs font-mono font-bold transition-colors"
                                       title="{{ $testimonial->property->title }}">
                                        {{ $testimonial->property->reference }}
                                    </a>
                                @else
                                    <span class="text-2xs text-slate-500 italic">General Experience</span>
                                @endif
                            </td>

                            <!-- Visibility Toggle -->
                            <td class="py-4 px-4 whitespace-nowrap">
                                <form method="POST" action="{{ route('admin.testimonials.toggle-publish', $testimonial) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit"
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-2xs font-bold cursor-pointer transition-colors {{ $testimonial->is_published ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 hover:bg-emerald-500/30' : 'bg-slate-800 text-slate-400 border border-slate-700 hover:bg-slate-700 hover:text-white' }}"
                                            title="Click to toggle publish status">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $testimonial->is_published ? 'bg-emerald-400' : 'bg-slate-500' }}"></span>
                                        {{ $testimonial->is_published ? 'Published' : 'Hidden' }}
                                    </button>
                                </form>
                            </td>

                            <!-- Actions -->
                            <td class="py-4 px-4 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.testimonials.edit', $testimonial) }}"
                                       class="p-1.5 text-slate-400 hover:text-white hover:bg-slate-800 rounded-lg transition-colors"
                                       title="Edit Testimonial">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>

                                    <form method="POST" action="{{ route('admin.testimonials.destroy', $testimonial) }}"
                                          onsubmit="return confirm('Are you sure you want to delete this testimonial from {{ addslashes($testimonial->client_name) }}?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="p-1.5 text-slate-400 hover:text-rose-400 hover:bg-rose-950/30 rounded-lg transition-colors cursor-pointer"
                                                title="Delete Testimonial">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-500">
                                <div class="max-w-sm mx-auto space-y-3">
                                    <div class="w-12 h-12 rounded-2xl bg-slate-800 text-slate-500 flex items-center justify-center mx-auto">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                                        </svg>
                                    </div>
                                    <p class="font-bold text-white text-sm">No testimonials recorded yet</p>
                                    <p class="text-xs text-slate-400">Add client feedback to showcase verified buyer trust on your public homepage.</p>
                                    <div class="pt-2">
                                        <a href="{{ route('admin.testimonials.create') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs">
                                            Add First Testimonial
                                        </a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($testimonials->hasPages())
            <div class="p-4 border-t border-slate-800 bg-slate-950/40">
                {{ $testimonials->links() }}
            </div>
        @endif
    </div>

</div>
@endsection

