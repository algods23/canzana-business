@extends('layouts.app')

@section('title', $building['name'])
@section('page-title', $building['name'])

@section('breadcrumb')
    <a href="{{ route('properties.index') }}" class="hover:text-brand-600">Properties</a>
    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" /></svg>
    <a href="{{ route('properties.show', $property['id']) }}" class="hover:text-brand-600">{{ $property['name'] }}</a>
    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" /></svg>
    <span class="text-slate-700">{{ $building['name'] }}</span>
@endsection

@section('header-actions')
    <div class="flex gap-2">
        <a href="{{ route('properties.buildings.edit', [$property['id'], $building['id']]) }}" class="btn btn-secondary">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125" /></svg>
            Edit Building
        </a>
        <a href="{{ route('properties.rooms.create', [$property['id'], $building['id']]) }}" class="btn btn-primary">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
            {{ $building['type'] === 'house' ? 'Add Section' : 'Add Room' }}
        </a>
    </div>
@endsection

@section('content')
    <div class="mb-6 grid grid-cols-2 gap-4 sm:grid-cols-4">
        <x-stat-card label="Total Rooms" :value="$building['rooms_count']" icon="room" />
        <x-stat-card label="Occupied" :value="$building['occupied']" icon="user" color="emerald" />
        <x-stat-card label="Vacant" :value="$building['rooms_count'] - $building['occupied']" icon="room" color="amber" />
        <x-stat-card label="Floors" :value="$building['floors']" icon="building" color="sky" />
    </div>

    {{-- Room Grid View --}}
    <div class="panel">
        <div class="flex flex-col gap-3 border-b border-border px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h3 class="font-semibold text-slate-900">{{ $building['type'] === 'house' ? 'House Details & Sections' : 'Rooms & Units' }}</h3>
                <p class="text-xs text-slate-500">Click a unit to view tenant details and payment history</p>
            </div>
            <div class="flex gap-2">
                <select class="input-field w-auto py-1.5 text-xs">
                    <option>All Status</option>
                    <option>Occupied</option>
                    <option>Vacant</option>
                    <option>Maintenance</option>
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-3 p-5 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
            @foreach($rooms as $room)
                @php
                    $displayStatus = $room['tenant'] ? $room['status'] : ($room['status'] === 'occupied' ? 'vacant' : $room['status']);
                    $statusColors = [
                        'occupied' => 'border-emerald-200 bg-emerald-50/50 hover:border-emerald-300',
                        'vacant' => 'border-slate-200 bg-slate-50/50 hover:border-slate-300',
                        'maintenance' => 'border-amber-200 bg-amber-50/50 hover:border-amber-300',
                    ];
                @endphp
                <a href="{{ route('properties.room', [$property['id'], $building['id'], $room['id']]) }}"
                   class="rounded-xl border p-4 transition-all hover:shadow-md {{ $statusColors[$displayStatus] ?? $statusColors['vacant'] }}">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-lg font-bold text-slate-900">{{ $room['unit'] }}</p>
                            <p class="text-xs text-slate-500">Floor {{ $room['floor'] }} · {{ $room['type'] }}</p>
                        </div>
                        <x-status-badge :status="$displayStatus" />
                    </div>
                    <div class="mt-3 space-y-1 text-sm">
                        <p class="text-slate-600">{{ $room['size_sqm'] }} sqm</p>
                        <p class="font-semibold text-slate-900">₱{{ number_format($room['rent']) }}/mo</p>
                    </div>
                    @if($room['tenant'])
                        <div class="mt-3 flex items-center gap-2 border-t border-border/50 pt-3">
                            <div class="flex h-7 w-7 items-center justify-center rounded-full bg-brand-100 text-xs font-semibold text-brand-700">
                                {{ strtoupper(substr($room['tenant'], 0, 1)) }}
                            </div>
                            <span class="truncate text-xs font-medium text-slate-700">{{ $room['tenant'] }}</span>
                        </div>
                    @else
                        <p class="mt-3 border-t border-border/50 pt-3 text-xs italic text-slate-400">No tenant assigned</p>
                    @endif
                </a>
            @endforeach
        </div>
    </div>
@endsection
