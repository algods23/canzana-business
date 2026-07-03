@extends('layouts.app')

@section('title', 'Activity Log')
@section('page-title', 'Activity Log')

@section('content')
    <div class="panel mb-6 p-4">
        <form method="GET" action="{{ route('activity.index') }}" class="flex flex-col gap-3 sm:flex-row sm:items-center">
            <select name="type" class="input-field sm:w-48">
                <option value="">All Activity Types</option>
                <option value="payment" @selected(($filters['type'] ?? '') === 'payment')>Payments</option>
                <option value="tenant" @selected(($filters['type'] ?? '') === 'tenant')>Tenants</option>
                <option value="room" @selected(($filters['type'] ?? '') === 'room')>Rooms</option>
                <option value="property" @selected(($filters['type'] ?? '') === 'property')>Properties</option>
                <option value="maintenance" @selected(($filters['type'] ?? '') === 'maintenance')>Maintenance</option>
                <option value="alert" @selected(($filters['type'] ?? '') === 'alert')>Alerts</option>
            </select>
            <button type="submit" class="btn btn-secondary">Filter</button>
        </form>
    </div>

    <div class="panel">
        <div class="divide-y divide-border">
            @forelse($activities as $activity)
                @php
                    $typeStyles = [
                        'payment' => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-600', 'badge' => 'badge-success'],
                        'alert' => ['bg' => 'bg-rose-50', 'text' => 'text-rose-600', 'badge' => 'badge-danger'],
                        'maintenance' => ['bg' => 'bg-amber-50', 'text' => 'text-amber-600', 'badge' => 'badge-warning'],
                        'tenant' => ['bg' => 'bg-sky-50', 'text' => 'text-sky-600', 'badge' => 'badge-info'],
                        'room' => ['bg' => 'bg-violet-50', 'text' => 'text-violet-600', 'badge' => 'badge-neutral'],
                        'property' => ['bg' => 'bg-brand-50', 'text' => 'text-brand-600', 'badge' => 'badge-neutral'],
                    ];
                    $style = $typeStyles[$activity['type']] ?? $typeStyles['property'];
                @endphp
                <div class="flex gap-4 px-5 py-4 transition-colors hover:bg-slate-50/50">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl {{ $style['bg'] }} {{ $style['text'] }}">
                        @include('components.icons.' . $activity['icon'], ['class' => 'h-5 w-5'])
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <h3 class="font-semibold text-slate-900">{{ $activity['title'] }}</h3>
                            <span class="badge {{ $style['badge'] }}">{{ ucfirst($activity['type']) }}</span>
                        </div>
                        <p class="mt-0.5 text-sm text-slate-600">{{ $activity['description'] }}</p>
                        <div class="mt-2 flex flex-wrap items-center gap-3 text-xs text-slate-400">
                            <span>{{ $activity['user'] }}</span>
                            <span>·</span>
                            <span>{{ $activity['entity'] }}</span>
                            <span>·</span>
                            <span>{{ $activity['time'] }}</span>
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-12 text-center">
                    <p class="text-slate-500">No activity records found</p>
                </div>
            @endforelse
        </div>
    </div>
@endsection
