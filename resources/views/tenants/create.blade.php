@extends('layouts.app')

@php
    $fromRoom = request()->has('room_id');
    $pageTitle = $fromRoom ? 'Assign Tenant' : 'Create Tenant';
    $submitLabel = $fromRoom ? 'Assign Tenant' : 'Create tenant';
@endphp

@section('title', $pageTitle)
@section('page-title', $pageTitle)

@section('header-actions')
    <button type="button" onclick="history.back()" class="btn btn-secondary">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" /></svg>
        Back
    </button>
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
