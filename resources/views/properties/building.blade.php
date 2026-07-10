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
        <button type="button" onclick="openDeleteModal()" class="btn btn-danger">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" /></svg>
            Delete Building
        </button>
        @if(($building['rental_mode'] ?? 'rooms') !== 'whole')
        <a href="{{ route('properties.rooms.create', [$property['id'], $building['id']]) }}" class="btn btn-primary">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
            {{ $building['type'] === 'house' ? 'Add Section' : 'Add Room' }}
        </a>
        @endif
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
                    $roomUrl = $room->currentTenant
                        ? route('tenants.show', $room->currentTenant)
                        : route('properties.room', [$property['id'], $building['id'], $room['id']]);
                @endphp
                <a href="{{ $roomUrl }}"
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

@section('scripts')
    <div id="deleteModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50">
        <div class="w-full max-w-md rounded-lg bg-white p-6 shadow-xl">
            <h3 class="mb-4 text-lg font-semibold text-slate-900">Confirm Deletion</h3>
            <p class="mb-4 text-sm text-slate-600">Are you sure you want to delete this building? This action cannot be undone and will also delete all rooms within this building.</p>
            
            <form id="deleteForm" method="POST" action="{{ route('properties.buildings.destroy', [$property['id'], $building['id']]) }}">
                @csrf
                @method('DELETE')
                
                <div class="mb-4">
                    <label class="mb-1.5 block text-sm font-medium text-slate-700" for="password">Enter your password to confirm</label>
                    <input id="password" name="password" type="password" class="input-field w-full" required>
                    @error('password')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
                </div>
                
                <div class="flex justify-end gap-3">
                    <button type="button" onclick="closeDeleteModal()" class="btn btn-secondary">Cancel</button>
                    <button type="submit" class="btn btn-danger">Delete Building</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openDeleteModal() {
            document.getElementById('deleteModal').classList.remove('hidden');
            document.getElementById('deleteModal').classList.add('flex');
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.add('hidden');
            document.getElementById('deleteModal').classList.remove('flex');
            document.getElementById('password').value = '';
        }

        // Close modal when clicking outside
        document.getElementById('deleteModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeDeleteModal();
            }
        });
    </script>
@endsection
