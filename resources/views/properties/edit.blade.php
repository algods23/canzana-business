@extends('layouts.app')

@section('title', 'Edit Property')
@section('page-title', 'Edit Property')

@section('header-actions')
    <a href="{{ route('properties.show', $property) }}" class="btn btn-secondary">Back</a>
@endsection

@section('content')
    <div class="panel p-6">
        <form method="POST" action="{{ route('properties.update', $property) }}" class="space-y-6">
            @csrf
            @method('PUT')
            @include('properties.form', ['property' => $property])
            <div class="flex items-center justify-between mt-6 border-t border-slate-100 pt-6">
                <button type="button" class="btn btn-secondary text-rose-600 border-rose-200 hover:bg-rose-50 hover:text-rose-700" onclick="if(confirm('Are you sure you want to delete this property? All associated buildings and rooms will be deleted. This action cannot be undone.')) document.getElementById('delete-property-form').submit();">Delete Property</button>
                <div class="flex items-center gap-3">
                    <a href="{{ route('properties.show', $property) }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
            </div>
        </form>
        <form id="delete-property-form" action="{{ route('properties.destroy', $property) }}" method="POST" class="hidden">
            @csrf
            @method('DELETE')
        </form>
    </div>
@endsection
