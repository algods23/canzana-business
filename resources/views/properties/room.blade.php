@extends('layouts.app')

@section('title', 'Unit ' . $room['unit'])
@section('page-title', 'Unit ' . $room['unit'])

@section('breadcrumb')
    <a href="{{ route('properties.index') }}" class="hover:text-brand-600">Properties</a>
    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" /></svg>
    <a href="{{ route('properties.show', $property['id']) }}" class="hover:text-brand-600">{{ $property['name'] }}</a>
    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" /></svg>
    <a href="{{ route('properties.building', [$property['id'], $building['id']]) }}" class="hover:text-brand-600">{{ $building['name'] }}</a>
    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" /></svg>
    <span class="text-slate-700">{{ $room['unit'] }}</span>
@endsection

@section('header-actions')
    <a href="{{ route('properties.rooms.edit', [$property['id'], $building['id'], $room['id']]) }}" class="btn btn-secondary">Edit Unit</a>
    @if($room['status'] === 'vacant')
        <a href="{{ route('tenants.create', ['property_id' => $property['id'], 'room_id' => $room['id']]) }}" class="btn btn-primary">Assign Tenant</a>
    @endif
@endsection

@section('content')
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        {{-- Unit Details --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="panel p-6">
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div>
                        <div class="flex items-center gap-3">
                            <h2 class="text-2xl font-bold text-slate-900">{{ $room['unit'] }}</h2>
                            <x-status-badge :status="$room['status']" />
                        </div>
                        <p class="mt-1 text-sm text-slate-500">{{ $building['name'] }} · {{ $property['name'] }}</p>
                    </div>
                    <p class="text-2xl font-bold text-brand-700">₱{{ number_format($room['rent']) }}<span class="text-sm font-normal text-slate-500">/month</span></p>
                </div>

                <div class="mt-6 grid grid-cols-2 gap-4 sm:grid-cols-4">
                    <div class="rounded-lg bg-slate-50 p-3">
                        <p class="text-xs text-slate-500">Type</p>
                        <p class="mt-0.5 font-semibold text-slate-900">{{ $room['type'] }}</p>
                    </div>
                    <div class="rounded-lg bg-slate-50 p-3">
                        <p class="text-xs text-slate-500">Floor</p>
                        <p class="mt-0.5 font-semibold text-slate-900">{{ $room['floor'] }}</p>
                    </div>
                    <div class="rounded-lg bg-slate-50 p-3">
                        <p class="text-xs text-slate-500">Size</p>
                        <p class="mt-0.5 font-semibold text-slate-900">{{ $room['size_sqm'] }} sqm</p>
                    </div>
                    <div class="rounded-lg bg-slate-50 p-3">
                        <p class="text-xs text-slate-500">Building</p>
                        <p class="mt-0.5 font-semibold text-slate-900">{{ $building['name'] }}</p>
                    </div>
                </div>
            </div>

            {{-- Payment History --}}
            <div class="panel">
                <div class="flex items-center justify-between border-b border-border px-5 py-4">
                    <h3 class="font-semibold text-slate-900">Payment History</h3>
                    <button type="button" class="btn btn-primary py-1.5 text-xs">Record Payment</button>
                </div>
                <div class="overflow-x-auto">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Due Date</th>
                                <th>Amount</th>
                                <th>Paid Date</th>
                                <th>Method</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($payments as $payment)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($payment['due_date'])->format('M d, Y') }}</td>
                                    <td class="font-medium">₱{{ number_format($payment['amount']) }}</td>
                                    <td>{{ $payment['paid_date'] ? \Carbon\Carbon::parse($payment['paid_date'])->format('M d, Y') : '—' }}</td>
                                    <td>{{ $payment['method'] ?? '—' }}</td>
                                    <td><x-status-badge :status="$payment['status']" /></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Activity Log --}}
            <div class="panel">
                <div class="border-b border-border px-5 py-4">
                    <h3 class="font-semibold text-slate-900">Activity Log</h3>
                </div>
                <div class="divide-y divide-border">
                    @foreach($activities as $activity)
                        <div class="flex gap-3 px-5 py-3">
                            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-slate-100 text-slate-600">
                                @include('components.icons.' . $activity['icon'], ['class' => 'h-4 w-4'])
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-slate-900">{{ $activity['title'] }}</p>
                                <p class="text-xs text-slate-500">{{ $activity['description'] }}</p>
                            </div>
                            <span class="text-xs text-slate-400">{{ $activity['time'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Tenant Sidebar --}}
        <div class="space-y-6">
            @if($room['tenant'])
                <div class="panel p-6">
                    <h3 class="font-semibold text-slate-900">Current Tenant</h3>
                    <div class="mt-4 flex items-center gap-4">
                        <div class="flex h-14 w-14 items-center justify-center rounded-full bg-brand-100 text-xl font-bold text-brand-700">
                            {{ strtoupper(substr($room['tenant'], 0, 1)) }}
                        </div>
                        <div>
                            <p class="font-semibold text-slate-900">{{ $room['tenant'] }}</p>
                            <p class="text-sm text-slate-500">Active tenant</p>
                        </div>
                    </div>
                    <dl class="mt-5 space-y-3 text-sm">
                        <div class="flex justify-between">
                            <dt class="text-slate-500">Lease Start</dt>
                            <dd class="font-medium text-slate-900">Jun 1, 2024</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-slate-500">Lease End</dt>
                            <dd class="font-medium text-slate-900">May 31, 2025</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-slate-500">Monthly Rent</dt>
                            <dd class="font-medium text-slate-900">₱{{ number_format($room['rent']) }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-slate-500">Balance</dt>
                            <dd class="font-medium text-emerald-600">₱0.00</dd>
                        </div>
                    </dl>
                    <button type="button" class="btn btn-secondary mt-5 w-full">View Tenant Profile</button>
                </div>
            @else
                <div class="panel p-6 text-center">
                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-slate-100">
                        @include('components.icons.user', ['class' => 'h-7 w-7 text-slate-400'])
                    </div>
                    <h3 class="mt-3 font-semibold text-slate-900">No Tenant</h3>
                    <p class="mt-1 text-sm text-slate-500">This unit is currently {{ $room['status'] }}</p>
                    @if($room['status'] === 'vacant')
                        <a href="{{ route('tenants.create', ['property_id' => $property['id'], 'room_id' => $room['id']]) }}" class="btn btn-primary mt-4 w-full">Assign Tenant</a>
                    @endif
                </div>
            @endif

            {{-- Rental Contract --}}
            <div class="panel p-6">
                <h3 class="font-semibold text-slate-900">Rental Contract</h3>
                @if($room['tenant'])
                    <div class="mt-4 space-y-3">
                        <div class="flex items-center justify-between rounded-lg border border-border p-3">
                            <div class="flex items-center gap-3">
                                <svg class="h-8 w-8 text-brand-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" /></svg>
                                <div>
                                    <p class="text-sm font-medium text-slate-900">Lease Agreement 2024</p>
                                    <p class="text-xs text-slate-500">PDF · 245 KB</p>
                                </div>
                            </div>
                            <button type="button" class="text-brand-600 hover:text-brand-700">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" /></svg>
                            </button>
                        </div>
                        <button type="button" class="btn btn-secondary w-full text-xs">Upload New Contract</button>
                    </div>
                @else
                    <p class="mt-3 text-sm text-slate-500">No contract on file. Assign a tenant to create a lease.</p>
                @endif
            </div>
        </div>
    </div>
@endsection
