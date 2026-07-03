@extends('layouts.app')

@section('title', 'Tenants')
@section('page-title', 'Tenants')

@section('header-actions')
    <a href="{{ route('tenants.create') }}" class="btn btn-primary">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
        Add Tenant
    </a>
@endsection

@section('content')
    <div class="panel mb-6 p-4">
        <form method="GET" action="{{ route('tenants.index') }}" class="flex flex-col gap-3 sm:flex-row sm:items-center">
            <div class="relative flex-1">
                <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" /></svg>
                <input type="search" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Search tenants by name or unit..." class="input-field pl-9">
            </div>
            <select name="status" class="input-field sm:w-40">
                <option value="">All Status</option>
                <option value="active" @selected(($filters['status'] ?? '') === 'active')>Active</option>
                <option value="overdue" @selected(($filters['status'] ?? '') === 'overdue')>Overdue</option>
            </select>
            <button type="submit" class="btn btn-secondary">Filter</button>
        </form>
    </div>

    <div class="panel overflow-hidden">
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Tenant</th>
                        <th>Property / Unit</th>
                        <th>Contact</th>
                        <th>Lease Period</th>
                        <th>Monthly Rent</th>
                        <th>Balance</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tenants as $tenant)
                        <tr>
                            <td>
                                <div class="flex items-center gap-3">
                                    <div class="flex h-9 w-9 items-center justify-center rounded-full {{ $tenant['status'] === 'overdue' ? 'bg-rose-100 text-rose-700' : 'bg-brand-100 text-brand-700' }} text-sm font-semibold">
                                        {{ strtoupper(substr($tenant['name'], 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="font-medium text-slate-900">{{ $tenant['name'] }}</p>
                                        @if($tenant['company'])
                                            <p class="text-xs text-slate-500">{{ $tenant['company'] }}</p>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                <p class="font-medium text-slate-700">{{ $tenant['property'] }}</p>
                                <p class="text-xs text-slate-500">Unit {{ $tenant['unit'] }}</p>
                            </td>
                            <td>
                                <p class="text-sm">{{ $tenant['email'] }}</p>
                                <p class="text-xs text-slate-500">{{ $tenant['phone'] }}</p>
                            </td>
                            <td class="text-sm">
                                {{ \Carbon\Carbon::parse($tenant['lease_start'])->format('M Y') }} — {{ \Carbon\Carbon::parse($tenant['lease_end'])->format('M Y') }}
                            </td>
                            <td class="font-medium">₱{{ number_format($tenant['rent']) }}</td>
                            <td class="{{ $tenant['balance'] > 0 ? 'font-semibold text-rose-600' : 'text-emerald-600' }}">
                                ₱{{ number_format($tenant['balance']) }}
                            </td>
                            <td><x-status-badge :status="$tenant['status']" /></td>
                            <td>
                                <a href="{{ route('tenants.show', $tenant['id']) }}" class="btn btn-ghost px-2 py-1 text-brand-600">View →</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
