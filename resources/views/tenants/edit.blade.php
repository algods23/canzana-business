@extends('layouts.app')

@section('title', 'Edit Tenant')
@section('page-title', 'Edit Tenant')

@section('header-actions')
    <a href="{{ route('tenants.show', $tenant) }}" class="btn btn-secondary">Back</a>
@endsection

@section('content')
    <div class="panel p-6">
        <form method="POST" action="{{ route('tenants.update', $tenant) }}" class="space-y-6">
            @csrf
            @method('PUT')
            @include('tenants.form', ['tenant' => $tenant])
            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('tenants.show', $tenant) }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Save changes</button>
            </div>
        </form>
    </div>
@endsection
