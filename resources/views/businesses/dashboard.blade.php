@extends('layouts.app')

@section('title', $business->name.' Dashboard')
@section('page-title', $business->name.' Dashboard')

@section('header-actions')
    <a href="{{ route('businesses.select') }}" class="btn btn-secondary">Switch Business</a>
@endsection

@section('content')
    <div class="panel p-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-wider text-brand-600">{{ str_replace('-', ' ', $business->type) }}</p>
                <h2 class="mt-1 text-2xl font-bold text-slate-900">{{ $business->name }}</h2>
                <p class="mt-2 max-w-2xl text-sm text-slate-500">{{ $business->description ?: 'This dashboard is ready for business-specific tools.' }}</p>
            </div>
            <span class="badge badge-success">{{ ucfirst($business->status) }}</span>
        </div>

        <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-3">
            <div class="rounded-lg border border-border bg-slate-50 p-4">
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Workspace</p>
                <p class="mt-2 text-lg font-bold text-slate-900">Active</p>
            </div>
            <div class="rounded-lg border border-border bg-slate-50 p-4">
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Type</p>
                <p class="mt-2 text-lg font-bold capitalize text-slate-900">{{ str_replace('-', ' ', $business->type) }}</p>
            </div>
            <div class="rounded-lg border border-border bg-slate-50 p-4">
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">Next Step</p>
                <p class="mt-2 text-lg font-bold text-slate-900">Build tools</p>
            </div>
        </div>
    </div>
@endsection
