@extends('layouts.admin')

@section('title', 'Site Inspection Appointments')
@section('header', 'Site Inspections Schedule')

@section('content')
<div class="space-y-6">

    <!-- Header Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-white tracking-tight">Site Inspections Agenda</h2>
            <p class="text-xs text-slate-400 mt-1">
                Coordinate client site visits, confirm physical meeting points, and record post-inspection outcomes.
            </p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('admin.inspections.create') }}"
               class="px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-lg shadow-emerald-950 transition-all flex items-center gap-2 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Schedule Site Inspection
            </a>
        </div>
    </div>

    <!-- Upcoming vs Past Tabs -->
    <div class="flex items-center gap-2 border-b border-slate-800 pb-2 text-xs font-semibold">
        <a href="{{ route('admin.inspections.index', ['tab' => 'upcoming']) }}"
           class="px-4 py-2 rounded-xl transition-colors flex items-center gap-2 {{ $tab !== 'past' ? 'bg-emerald-600 text-white font-bold' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">
            <span>Upcoming Inspections</span>
            <span class="px-2 py-0.5 rounded-full text-3xs font-mono {{ $tab !== 'past' ? 'bg-emerald-800 text-white' : 'bg-slate-800 text-slate-400' }}">
                {{ $upcomingCount }}
            </span>
        </a>

        <a href="{{ route('admin.inspections.index', ['tab' => 'past']) }}"
           class="px-4 py-2 rounded-xl transition-colors flex items-center gap-2 {{ $tab === 'past' ? 'bg-emerald-600 text-white font-bold' : 'text-slate-400 hover:text-white hover:bg-slate-800' }}">
            <span>Past / Archive</span>
            <span class="px-2 py-0.5 rounded-full text-3xs font-mono {{ $tab === 'past' ? 'bg-emerald-800 text-white' : 'bg-slate-800 text-slate-400' }}">
                {{ $pastCount }}
            </span>
        </a>
    </div>

    @if (session('status'))
        <div class="p-4 rounded-2xl bg-emerald-950/80 border border-emerald-800 text-emerald-300 text-xs flex items-center gap-2">
            <svg class="w-4 h-4 shrink-0 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            <span>{{ session('status') }}</span>
        </div>
    @endif

    <!-- Inspections Agenda Cards -->
    <div class="space-y-4">
        @forelse ($inspections as $inspection)
            <div class="p-5 sm:p-6 rounded-2xl bg-slate-900 border border-slate-800 hover:border-slate-700 transition-all shadow-xl flex flex-col md:flex-row md:items-center md:justify-between gap-5">
                <!-- Date & Time Column -->
                <div class="flex items-center gap-4 shrink-0">
                    <div class="w-16 h-16 rounded-2xl bg-slate-950 border border-slate-800 flex flex-col items-center justify-center text-center">
                        <span class="text-3xs uppercase tracking-wider font-extrabold text-emerald-400 font-mono">
                            {{ $inspection->scheduled_date->format('M') }}
                        </span>
                        <span class="text-xl font-black text-white font-mono">
                            {{ $inspection->scheduled_date->format('d') }}
                        </span>
                        <span class="text-3xs text-slate-500 font-mono">
                            {{ $inspection->scheduled_date->format('D') }}
                        </span>
                    </div>

                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-bold text-white">{{ $inspection->scheduled_time }}</span>
                            @php
                                $statusStyle = match ($inspection->status) {
                                    'confirmed' => 'bg-emerald-950 text-emerald-300 border-emerald-800',
                                    'pending_confirmation' => 'bg-amber-950 text-amber-300 border-amber-800',
                                    'rescheduled' => 'bg-purple-950 text-purple-300 border-purple-800',
                                    'completed' => 'bg-blue-950 text-blue-300 border-blue-800',
                                    'cancelled' => 'bg-rose-950 text-rose-300 border-rose-800',
                                    default => 'bg-slate-800 text-slate-300 border-slate-700',
                                };
                            @endphp
                            <span class="px-2 py-0.5 rounded text-3xs font-extrabold uppercase tracking-wider border {{ $statusStyle }}">
                                {{ \App\Models\Inspection::STATUSES[$inspection->status] ?? $inspection->status }}
                            </span>
                        </div>
                        <p class="text-xs text-slate-400 mt-1 flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span>Meeting: <strong class="text-slate-200">{{ $inspection->meeting_location }}</strong></span>
                        </p>
                    </div>
                </div>

                <!-- Property & Client Middle Column -->
                <div class="space-y-1.5 md:border-l md:border-slate-800 md:pl-5 flex-1">
                    <div class="flex items-center gap-2">
                        <span class="font-mono text-2xs text-emerald-400 font-bold">[{{ $inspection->property->reference }}]</span>
                        <a href="{{ route('admin.properties.show', $inspection->property) }}" class="text-sm font-bold text-white hover:text-emerald-400 truncate">
                            {{ $inspection->property->title }}
                        </a>
                    </div>
                    <p class="text-xs text-slate-400">
                        Client: <a href="{{ route('admin.customers.show', $inspection->customer) }}" class="text-emerald-400 hover:underline font-semibold">{{ $inspection->customer->name }}</a>
                        <span class="text-slate-600">•</span>
                        <span class="font-mono text-slate-300">{{ $inspection->customer->phone }}</span>
                    </p>
                    @if ($inspection->notes)
                        <p class="text-2xs text-slate-400 italic line-clamp-1">
                            Note: {{ $inspection->notes }}
                        </p>
                    @endif
                </div>

                <!-- Right Action Buttons -->
                <div class="flex items-center gap-2 shrink-0 pt-2 md:pt-0">
                    @php
                        $cleanPhone = preg_replace('/[^0-9]/', '', $inspection->customer->phone);
                        if (str_starts_with($cleanPhone, '0') && strlen($cleanPhone) === 11) {
                            $waPhone = '234' . substr($cleanPhone, 1);
                        } else {
                            $waPhone = $cleanPhone;
                        }
                        $waReminder = "Hello {$inspection->customer->name}, this is a reminder regarding our scheduled site inspection for property [{$inspection->property->reference}] on {$inspection->formatted_date} at {$inspection->scheduled_time}. Meeting point: {$inspection->meeting_location}. See you then!";
                    @endphp
                    <a href="https://wa.me/{{ $waPhone }}?text={{ urlencode($waReminder) }}" target="_blank"
                       title="Send WhatsApp confirmation reminder"
                       class="p-2.5 rounded-xl bg-emerald-950/80 hover:bg-emerald-900 border border-emerald-800 text-emerald-400 transition-colors">
                        <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
                            <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
                        </svg>
                    </a>

                    <a href="{{ route('admin.inspections.show', $inspection) }}"
                       class="px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-white font-semibold text-xs transition-colors">
                        View Agenda
                    </a>

                    <a href="{{ route('admin.inspections.edit', $inspection) }}"
                       class="px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold text-xs transition-colors">
                        Reschedule
                    </a>
                </div>
            </div>
        @empty
            <div class="py-16 text-center rounded-2xl bg-slate-900 border border-slate-800">
                <div class="w-12 h-12 rounded-2xl bg-slate-800 flex items-center justify-center text-slate-500 mx-auto mb-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <p class="text-slate-300 font-semibold text-sm">No inspections in this view</p>
                <p class="text-slate-500 text-xs mt-1">
                    {{ $tab === 'past' ? 'No past inspections archived.' : 'No upcoming site visits currently scheduled.' }}
                </p>
            </div>
        @endforelse
    </div>

    @if ($inspections->hasPages())
        <div class="p-4 rounded-2xl bg-slate-900 border border-slate-800">
            {{ $inspections->links() }}
        </div>
    @endif

</div>
@endsection

