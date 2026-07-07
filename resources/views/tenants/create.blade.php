@extends('layouts.app')

@php
    $fromRoom = request()->has('room_id');
    $pageTitle = $fromRoom ? 'Assign Tenant' : 'Create Tenant';
    $submitLabel = $fromRoom ? 'Assign Tenant' : 'Create tenant';
@endphp

@section('title', $pageTitle)
@section('page-title', $pageTitle)

@section('header-actions')
    @if(request()->has('room_id') && request('property_id'))
        {{-- Back to the room if we came from one --}}
        @php
            $backRoom = \App\Models\Room::with('buildingModel')->find(request('room_id'));
        @endphp
        @if($backRoom)
            <a href="{{ route('properties.room', [$backRoom->buildingModel->property_id, $backRoom->building_id, $backRoom->id]) }}" class="btn btn-secondary">Cancel</a>
        @else
            <a href="{{ route('tenants.index') }}" class="btn btn-secondary">Cancel</a>
        @endif
    @else
        <a href="{{ route('tenants.index') }}" class="btn btn-secondary">Cancel</a>
    @endif
@endsection

@section('content')
    <div class="panel p-6">
        <form method="POST" action="{{ route('tenants.store') }}" class="space-y-6">
            @include('tenants.form', [
                'tenant'          => $tenant,
                'existingTenants' => $fromRoom ? ($existingTenants ?? collect()) : collect(),
            ])
            <div class="flex items-center justify-end gap-3">
                @if(request()->has('room_id') && request('property_id'))
                    @if(isset($backRoom) && $backRoom)
                        <a href="{{ route('properties.room', [$backRoom->buildingModel->property_id, $backRoom->building_id, $backRoom->id]) }}" class="btn btn-secondary">Cancel</a>
                    @else
                        <a href="{{ route('tenants.index') }}" class="btn btn-secondary">Cancel</a>
                    @endif
                @else
                    <a href="{{ route('tenants.index') }}" class="btn btn-secondary">Cancel</a>
                @endif
                <button type="submit" class="btn btn-primary">{{ $submitLabel }}</button>
            </div>
        </form>
    </div>
@endsection
