@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
    {{-- Overdue Alert Banner --}}
    @if($stats['overdue_count'] > 0)
        <div data-alert class="mb-6 flex flex-col gap-3 rounded-xl border border-rose-200 bg-rose-50 p-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-start gap-3">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-rose-100 text-rose-600">
                    @include('components.icons.alert', ['class' => 'h-5 w-5'])
                </div>
                <div>
                    <p class="font-semibold text-rose-900">{{ $stats['overdue_count'] }} overdue payment{{ $stats['overdue_count'] > 1 ? 's' : '' }}</p>
                    <p class="text-sm text-rose-700">Total outstanding: ₱{{ number_format($stats['overdue_amount']) }} — action required</p>
                </div>
            </div>
            <a href="{{ route('payments.index', ['status' => 'overdue']) }}" class="btn btn-primary shrink-0 bg-rose-600 hover:bg-rose-700 focus:ring-rose-500">Review Overdue</a>
        </div>
    @endif

    {{-- KPI Stats --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-stat-card label="Total Properties" :value="$stats['total_properties']" change="+1 this quarter" change-type="up" icon="property" color="brand" />
        <x-stat-card label="Occupancy Rate" :value="$stats['occupancy_rate'] . '%'" change="+2.3% vs last month" change-type="up" icon="building" color="emerald" />
        <x-stat-card label="Monthly Revenue" :value="'₱' . number_format($stats['monthly_revenue'])" change="82.8% collected" change-type="neutral" icon="revenue" color="sky" />
        <x-stat-card label="Overdue Amount" :value="'₱' . number_format($stats['overdue_amount'])" change="{{ $stats['overdue_count'] }} accounts" change-type="down" icon="alert" color="rose" />
    </div>

    {{-- Charts Row --}}
    <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-3">
        {{-- Revenue Chart --}}
        <div class="panel lg:col-span-2">
            <div class="flex items-center justify-between border-b border-border px-5 py-4">
                <div>
                    <h3 class="font-semibold text-slate-900">Revenue Overview</h3>
                    <p class="text-xs text-slate-500">Collected vs expected — last 6 months</p>
                </div>
                <select class="input-field w-auto py-1.5 text-xs">
                    <option>Last 6 months</option>
                    <option>Last 12 months</option>
                </select>
            </div>
            <div class="p-5">
                <div class="flex items-end justify-between gap-2 sm:gap-4" style="height: 200px;">
                    @foreach($revenueChart as $month)
                        @php
                            $maxExpected = max(array_column($revenueChart, 'expected'));
                            $collectedHeight = ($month['collected'] / $maxExpected) * 100;
                            $expectedHeight = ($month['expected'] / $maxExpected) * 100;
                        @endphp
                        <div class="flex flex-1 flex-col items-center gap-1">
                            <div class="relative flex w-full items-end justify-center gap-1" style="height: 160px;">
                                <div class="w-3 rounded-t bg-slate-200 sm:w-4" style="height: {{ $expectedHeight }}%" title="Expected"></div>
                                <div class="w-3 rounded-t bg-brand-500 sm:w-4" style="height: {{ $collectedHeight }}%" title="Collected"></div>
                            </div>
                            <span class="text-[10px] font-medium text-slate-500 sm:text-xs">{{ $month['month'] }}</span>
                        </div>
                    @endforeach
                </div>
                <div class="mt-4 flex items-center justify-center gap-6 text-xs text-slate-500">
                    <span class="flex items-center gap-1.5"><span class="h-2.5 w-2.5 rounded bg-brand-500"></span> Collected</span>
                    <span class="flex items-center gap-1.5"><span class="h-2.5 w-2.5 rounded bg-slate-200"></span> Expected</span>
                </div>
            </div>
        </div>

        {{-- Occupancy by Property --}}
        <div class="panel">
            <div class="border-b border-border px-5 py-4">
                <h3 class="font-semibold text-slate-900">Occupancy by Property</h3>
                <p class="text-xs text-slate-500">Current occupancy rates</p>
            </div>
            <div class="space-y-4 p-5">
                @foreach($occupancy as $item)
                    <div>
                        <div class="mb-1.5 flex items-center justify-between text-sm">
                            <span class="font-medium text-slate-700">{{ $item['name'] }}</span>
                            <span class="font-semibold text-brand-700">{{ $item['rate'] }}%</span>
                        </div>
                        <div class="h-2 overflow-hidden rounded-full bg-slate-100">
                            <div class="h-full rounded-full bg-brand-500 transition-all" style="width: {{ $item['rate'] }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Bottom Row --}}
    <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-2">
        {{-- Recent Activity --}}
        <div class="panel">
            <div class="flex items-center justify-between border-b border-border px-5 py-4">
                <h3 class="font-semibold text-slate-900">Recent Activity</h3>
                <a href="{{ route('activity.index') }}" class="text-xs font-medium text-brand-600 hover:text-brand-700">View all</a>
            </div>
            <div class="divide-y divide-border">
                @foreach($activities as $activity)
                    <div class="flex gap-3 px-5 py-3.5">
                        @php
                            $iconBg = match($activity['type']) {
                                'payment' => 'bg-emerald-50 text-emerald-600',
                                'alert' => 'bg-rose-50 text-rose-600',
                                'maintenance' => 'bg-amber-50 text-amber-600',
                                default => 'bg-sky-50 text-sky-600',
                            };
                        @endphp
                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg {{ $iconBg }}">
                            @include('components.icons.' . $activity['icon'], ['class' => 'h-4 w-4'])
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-medium text-slate-900">{{ $activity['title'] }}</p>
                            <p class="truncate text-xs text-slate-500">{{ $activity['description'] }}</p>
                        </div>
                        <span class="shrink-0 text-xs text-slate-400">{{ $activity['time'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Property Overview --}}
        <div class="panel">
            <div class="flex items-center justify-between border-b border-border px-5 py-4">
                <h3 class="font-semibold text-slate-900">Properties Overview</h3>
                <a href="{{ route('properties.index') }}" class="text-xs font-medium text-brand-600 hover:text-brand-700">View all</a>
            </div>
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Property</th>
                            <th>Rooms</th>
                            <th>Occupancy</th>
                            <th>Revenue</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($properties as $property)
                            <tr>
                                <td>
                                    <a href="{{ route('properties.show', $property['id']) }}" class="font-medium text-brand-700 hover:text-brand-800">{{ $property['name'] }}</a>
                                    <p class="text-xs text-slate-400">{{ $property['city'] }}</p>
                                </td>
                                <td>{{ $property['occupied_rooms'] }}/{{ $property['rooms_count'] }}</td>
                                <td>
                                    <span class="font-medium {{ $property['occupancy_rate'] >= 85 ? 'text-emerald-600' : 'text-amber-600' }}">{{ $property['occupancy_rate'] }}%</span>
                                </td>
                                <td class="font-medium">₱{{ number_format($property['monthly_revenue']) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
