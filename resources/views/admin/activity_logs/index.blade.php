@extends('layouts.admin')

@section('title', 'Administrative Activity Logs & Audit Trail')
@section('header', 'Audit Trail & Activity Logs')

@section('content')
<div class="space-y-6">

    <!-- Header Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-white tracking-tight">Administrative Activity Logs</h2>
            <p class="text-xs text-slate-400 mt-1">
                Security audit trail of property updates, lead management changes, inspection schedules, and settings modifications.
            </p>
        </div>

        <div class="flex items-center gap-2">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-2xs font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                Immutable Event Logging
            </span>
        </div>
    </div>

    <!-- Filters Bar -->
    <div class="p-4 rounded-2xl bg-slate-900 border border-slate-800">
        <form method="GET" action="{{ route('admin.activity-logs.index') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3 items-center">

            <!-- Action Filter -->
            <div>
                <select name="action"
                        class="w-full px-3.5 py-2 rounded-xl bg-slate-950 border border-slate-800 text-xs text-white focus:outline-hidden focus:border-emerald-500 transition-colors">
                    <option value="">All Event Types</option>
                    @foreach ($actions as $act)
                        <option value="{{ $act }}" {{ $currentAction === $act ? 'selected' : '' }}>
                            {{ $act }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Search Filter -->
            <div>
                <input type="text" name="search" value="{{ $search }}"
                       placeholder="Search event description, IP, or action..."
                       class="w-full px-3.5 py-2 rounded-xl bg-slate-950 border border-slate-800 text-xs text-white placeholder-slate-500 focus:outline-hidden focus:border-emerald-500 transition-colors">
            </div>

            <!-- Buttons -->
            <div class="flex items-center gap-2">
                <button type="submit"
                        class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-white text-xs font-bold transition-colors cursor-pointer">
                    Apply Filter
                </button>
                @if ($currentAction || $search)
                    <a href="{{ route('admin.activity-logs.index') }}"
                       class="px-3 py-2 rounded-xl text-xs text-slate-400 hover:text-white transition-colors">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Activity Log Table -->
    <div class="bg-slate-900 border border-slate-800 rounded-2xl overflow-hidden shadow-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-300">
                <thead class="bg-slate-950/60 text-3xs uppercase tracking-wider text-slate-400 border-b border-slate-800">
                    <tr>
                        <th class="py-3.5 px-4 font-bold">Timestamp</th>
                        <th class="py-3.5 px-4 font-bold">Admin Actor</th>
                        <th class="py-3.5 px-4 font-bold">Action Event</th>
                        <th class="py-3.5 px-4 font-bold">Event Summary</th>
                        <th class="py-3.5 px-4 font-bold">IP Address</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60 font-medium">
                    @forelse ($logs as $log)
                        <tr class="hover:bg-slate-800/30 transition-colors">
                            <!-- Timestamp -->
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <span class="font-mono text-white text-xs">{{ $log->created_at->format('M j, Y • H:i') }}</span>
                                <span class="block text-3xs text-slate-500">{{ $log->created_at->diffForHumans() }}</span>
                            </td>

                            <!-- Actor -->
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                @if ($log->user)
                                    <div class="flex items-center gap-2">
                                        <div class="w-6 h-6 rounded-md bg-emerald-600/30 text-emerald-400 flex items-center justify-center font-bold text-3xs">
                                            {{ strtoupper(substr($log->user->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <p class="font-semibold text-white text-xs">{{ $log->user->name }}</p>
                                            <p class="text-3xs text-slate-500">{{ $log->user->email }}</p>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-2xs text-slate-500 italic">System / Automated</span>
                                @endif
                            </td>

                            <!-- Action Badge -->
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                @php
                                    $badgeColor = match(explode('.', $log->action)[0] ?? '') {
                                        'property' => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/30',
                                        'lead' => 'bg-sky-500/10 text-sky-400 border-sky-500/30',
                                        'inspection' => 'bg-amber-500/10 text-amber-400 border-amber-500/30',
                                        'testimonial' => 'bg-purple-500/10 text-purple-400 border-purple-500/30',
                                        'settings' => 'bg-teal-500/10 text-teal-400 border-teal-500/30',
                                        default => 'bg-slate-800 text-slate-300 border-slate-700',
                                    };
                                @endphp
                                <span class="px-2.5 py-1 rounded-md text-3xs font-mono font-bold border {{ $badgeColor }}">
                                    {{ $log->action }}
                                </span>
                            </td>

                            <!-- Summary -->
                            <td class="py-3.5 px-4">
                                <p class="text-slate-200 text-xs">{{ $log->description }}</p>
                                @if (!empty($log->properties))
                                    <div class="mt-1 text-3xs font-mono text-slate-500 flex flex-wrap gap-2">
                                        @foreach ($log->properties as $k => $v)
                                            <span class="px-1.5 py-0.5 rounded bg-slate-950 border border-slate-800">
                                                {{ $k }}: {{ is_array($v) ? json_encode($v) : $v }}
                                            </span>
                                        @endforeach
                                    </div>
                                @endif
                            </td>

                            <!-- IP Address -->
                            <td class="py-3.5 px-4 whitespace-nowrap font-mono text-2xs text-slate-400">
                                {{ $log->ip_address ?: '127.0.0.1' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center text-slate-500">
                                <div class="max-w-sm mx-auto space-y-2">
                                    <p class="font-bold text-white text-sm">No activity events found</p>
                                    <p class="text-xs text-slate-400">Administrative actions will automatically generate audit logs here.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($logs->hasPages())
            <div class="p-4 border-t border-slate-800 bg-slate-950/40">
                {{ $logs->links() }}
            </div>
        @endif
    </div>

</div>
@endsection

