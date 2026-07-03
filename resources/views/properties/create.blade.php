@extends('layouts.app')

@section('title', 'Create Property')
@section('page-title', 'Create Property')

@section('header-actions')
    <a href="{{ route('properties.index') }}" class="btn btn-secondary">Cancel</a>
@endsection

@section('content')
    <div class="panel p-6">
        <form method="POST" action="{{ route('properties.store') }}" class="space-y-6">
            @include('properties.form', ['property' => $property])
            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('properties.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Create property</button>
            </div>
        </form>
    </div>
@endsection
