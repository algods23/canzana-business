@extends('layouts.app')

@section('title', 'Create Tenant')
@section('page-title', 'Create Tenant')

@section('header-actions')
    <a href="{{ route('tenants.index') }}" class="btn btn-secondary">Cancel</a>
@endsection

@section('content')
    <div class="panel p-6">
        <form method="POST" action="{{ route('tenants.store') }}" class="space-y-6">
            @include('tenants.form', ['tenant' => $tenant])
            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('tenants.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Create tenant</button>
            </div>
        </form>
    </div>
@endsection
