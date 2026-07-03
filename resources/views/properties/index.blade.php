@extends('layouts.app')

@section('title', 'Properties')
@section('page-title', 'Properties')

@section('header-actions')
    <button type="button" class="btn btn-primary">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
        Add Property
    </button>
@endsection

@section('content')
    {{-- Filters --}}
    <div class="panel mb-6 p-4">
        <form method="GET" action="{{ route('properties.index') }}" class="flex flex-col gap-3 sm:flex-row sm:items-center">
            <div class="relative flex-1">
                <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" /></svg>
                <input type="search" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Search by name or city..." class="input-field pl-9">
            </div>
            <select name="type" class="input-field sm:w-44">
                <option value="">All Types</option>
                <option value="Residential" @selected(($filters['type'] ?? '') === 'Residential')>Residential</option>
                <option value="Commercial" @selected(($filters['type'] ?? '') === 'Commercial')>Commercial</option>
                <option value="Mixed Use" @selected(($filters['type'] ?? '') === 'Mixed Use')>Mixed Use</option>
            </select>
            <button type="submit" class="btn btn-secondary">Filter</button>
        </form>
    </div>

    {{-- Property Grid --}}
    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3">
        @forelse($properties as $property)
            <a href="{{ route('properties.show', $property['id']) }}" class="panel group overflow-hidden transition-all hover:border-brand-300 hover:shadow-md">
                <div class="relative h-36 bg-gradient-to-br from-brand-700 to-brand-900">
                    <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width=\"60\" height=\"60\" viewBox=\"0 0 60 60\" xmlns=\"http://www.w3.org/2000/svg\"%3E%3Cg fill=\"none\" fill-rule=\"evenodd\"%3E%3Cg fill=\"%23ffffff\" fill-opacity=\"0.05\"%3E%3Cpath d=\"M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')] opacity-50"></div>
                    <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/60 to-transparent p-4">
                        <h3 class="text-lg font-bold text-white group-hover:text-brand-200">{{ $property['name'] }}</h3>
                        <p class="text-sm text-white/80">{{ $property['address'] }}, {{ $property['city'] }}</p>
                    </div>
                    <div class="absolute right-3 top-3">
                        <x-status-badge :status="$property['status']" />
                    </div>
                </div>
                <div class="grid grid-cols-3 divide-x divide-border border-t border-border">
                    <div class="px-4 py-3 text-center">
                        <p class="text-lg font-bold text-slate-900">{{ $property['buildings_count'] }}</p>
                        <p class="text-xs text-slate-500">Buildings</p>
                    </div>
                    <div class="px-4 py-3 text-center">
                        <p class="text-lg font-bold text-slate-900">{{ $property['rooms_count'] }}</p>
                        <p class="text-xs text-slate-500">Rooms</p>
                    </div>
                    <div class="px-4 py-3 text-center">
                        <p class="text-lg font-bold text-brand-700">{{ $property['occupancy_rate'] }}%</p>
                        <p class="text-xs text-slate-500">Occupied</p>
                    </div>
                </div>
                <div class="flex items-center justify-between border-t border-border px-4 py-3">
                    <span class="badge badge-neutral">{{ $property['type'] }}</span>
                    <span class="text-sm font-semibold text-slate-700">₱{{ number_format($property['monthly_revenue']) }}/mo</span>
                </div>
            </a>
        @empty
            <div class="col-span-full panel p-12 text-center">
                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-slate-100">
                    @include('components.icons.building', ['class' => 'h-8 w-8 text-slate-400'])
                </div>
                <h3 class="mt-4 text-lg font-semibold text-slate-900">No properties found</h3>
                <p class="mt-1 text-sm text-slate-500">Try adjusting your search or filters</p>
            </div>
        @endforelse
    </div>
@endsection
