@extends('layouts.app')

@section('title', $property['name'])
@section('page-title', $property['name'])

@section('breadcrumb')
    <a href="{{ route('properties.index') }}" class="hover:text-brand-600">Properties</a>
    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" /></svg>
    <span class="text-slate-700">{{ $property['name'] }}</span>
@endsection

@section('header-actions')
    <a href="{{ route('properties.edit', $property) }}" class="btn btn-secondary">Edit Property</a>
    <button type="button" class="btn btn-primary">Add Building</button>
@endsection

@section('content')
    {{-- Property Header --}}
    <div class="panel mb-6 overflow-hidden">
        <div class="relative h-48 bg-gradient-to-r from-brand-800 to-brand-600 sm:h-56">
            <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width=\"60\" height=\"60\" viewBox=\"0 0 60 60\" xmlns=\"http://www.w3.org/2000/svg\"%3E%3Cg fill=\"none\" fill-rule=\"evenodd\"%3E%3Cg fill=\"%23ffffff\" fill-opacity=\"0.05\"%3E%3Cpath d=\"M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')]"></div>
            <div class="absolute bottom-0 left-0 right-0 p-6 sm:p-8">
                <div class="flex flex-wrap items-end gap-3">
                    <x-status-badge :status="$property['status']" />
                    <span class="badge badge-neutral">{{ $property['type'] }}</span>
                </div>
                <p class="mt-2 text-white/90">{{ $property['address'] }}, {{ $property['city'] }}</p>
            </div>
        </div>
        <div class="grid grid-cols-2 divide-x divide-border border-t border-border sm:grid-cols-4">
            <div class="px-5 py-4 text-center sm:text-left">
                <p class="text-2xl font-bold text-slate-900">{{ $property['buildings_count'] }}</p>
                <p class="text-sm text-slate-500">Buildings</p>
            </div>
            <div class="px-5 py-4 text-center sm:text-left">
                <p class="text-2xl font-bold text-slate-900">{{ $property['occupied_rooms'] }}/{{ $property['rooms_count'] }}</p>
                <p class="text-sm text-slate-500">Rooms Occupied</p>
            </div>
            <div class="px-5 py-4 text-center sm:text-left">
                <p class="text-2xl font-bold text-brand-700">{{ $property['occupancy_rate'] }}%</p>
                <p class="text-sm text-slate-500">Occupancy Rate</p>
            </div>
            <div class="px-5 py-4 text-center sm:text-left">
                <p class="text-2xl font-bold text-slate-900">₱{{ number_format($property['monthly_revenue']) }}</p>
                <p class="text-sm text-slate-500">Monthly Revenue</p>
            </div>
        </div>
    </div>

    {{-- Tabs --}}
    <div class="mb-6 border-b border-border">
        <nav class="-mb-px flex gap-1 overflow-x-auto">
            <a href="#" class="tab-link tab-link-active whitespace-nowrap">Buildings</a>
            <a href="#" class="tab-link whitespace-nowrap">Overview</a>
            <a href="#" class="tab-link whitespace-nowrap">Tenants</a>
            <a href="#" class="tab-link whitespace-nowrap">Payments</a>
            <a href="#" class="tab-link whitespace-nowrap">Activity</a>
        </nav>
    </div>

    {{-- Buildings List --}}
    <div class="panel">
        <div class="flex items-center justify-between border-b border-border px-5 py-4">
            <div>
                <h3 class="font-semibold text-slate-900">Buildings</h3>
                <p class="text-xs text-slate-500">{{ count($buildings) }} building{{ count($buildings) !== 1 ? 's' : '' }} in this property</p>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Building Name</th>
                        <th>Floors</th>
                        <th>Rooms</th>
                        <th>Occupied</th>
                        <th>Occupancy</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($buildings as $building)
                        @php $rate = $building['rooms_count'] > 0 ? round(($building['occupied'] / $building['rooms_count']) * 100, 1) : 0; @endphp
                        <tr>
                            <td>
                                <div class="flex items-center gap-3">
                                    <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-brand-50 text-brand-700">
                                        @include('components.icons.building', ['class' => 'h-4 w-4'])
                                    </div>
                                    <span class="font-medium text-slate-900">{{ $building['name'] }}</span>
                                </div>
                            </td>
                            <td>{{ $building['floors'] }}</td>
                            <td>{{ $building['rooms_count'] }}</td>
                            <td>{{ $building['occupied'] }}</td>
                            <td>
                                <div class="flex items-center gap-2">
                                    <div class="h-1.5 w-16 overflow-hidden rounded-full bg-slate-100">
                                        <div class="h-full rounded-full bg-brand-500" style="width: {{ $rate }}%"></div>
                                    </div>
                                    <span class="text-sm font-medium">{{ $rate }}%</span>
                                </div>
                            </td>
                            <td><x-status-badge :status="$building['status']" /></td>
                            <td>
                                <a href="{{ route('properties.building', [$property['id'], $building['id']]) }}" class="btn btn-ghost px-2 py-1 text-brand-600">View Rooms →</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
