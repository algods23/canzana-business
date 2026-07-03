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
            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('properties.show', $property) }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Save changes</button>
            </div>
        </form>
    </div>
@endsection
