@extends('layouts.app')

@section('title', 'Edit Building')
@section('page-title', 'Edit Building')

@section('breadcrumb')
    <a href="{{ route('properties.index') }}" class="hover:text-brand-600">Properties</a>
    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" /></svg>
    <a href="{{ route('properties.show', $property) }}" class="hover:text-brand-600">{{ $property->name }}</a>
    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" /></svg>
    <a href="{{ route('properties.building', [$property, $building]) }}" class="hover:text-brand-600">{{ $building->name }}</a>
    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" /></svg>
    <span class="text-slate-700">Edit Building</span>
@endsection

@section('header-actions')
    <a href="{{ route('properties.building', [$property, $building]) }}" class="btn btn-secondary">Cancel</a>
@endsection

@section('content')
    <div class="panel p-6">
        <form method="POST" action="{{ route('properties.buildings.update', [$property, $building]) }}" class="space-y-6">
            @method('PUT')
            @include('properties.buildings.form', ['property' => $property, 'building' => $building])
            <div class="flex items-center justify-between mt-6 border-t border-slate-100 pt-6">
                <button type="button" class="btn btn-secondary text-rose-600 border-rose-200 hover:bg-rose-50 hover:text-rose-700" onclick="if(confirm('Are you sure you want to delete this building? All associated rooms will be deleted. This action cannot be undone.')) document.getElementById('delete-building-form').submit();">Delete Building</button>
                <div class="flex items-center gap-3">
                    <a href="{{ route('properties.building', [$property, $building]) }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Update building</button>
                </div>
            </div>
        </form>
        <form id="delete-building-form" action="{{ route('properties.buildings.destroy', [$property, $building]) }}" method="POST" class="hidden">
            @csrf
            @method('DELETE')
        </form>
    </div>
@endsection
