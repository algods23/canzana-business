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
@php
    $displayStatus = $room['tenant'] ? $room['status'] : ($room['status'] === 'occupied' ? 'vacant' : $room['status']);
@endphp
@endsection

@section('header-actions')
@endsection

@section('content')
    @if($room['status'] === 'maintenance')
        <div class="mb-6 flex items-center gap-3 rounded-xl border border-amber-200 bg-amber-50 p-4">
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-amber-100 text-amber-600">
                @include('components.icons.maintenance', ['class' => 'h-5 w-5'])
            </div>
            <div>
                <p class="font-semibold text-amber-900">Under Maintenance</p>
                <p class="text-sm text-amber-700">This room is currently under maintenance and unavailable for tenants.</p>
            </div>
        </div>
    @endif

    @php
        $today = \Carbon\Carbon::today();
        $leaseEnded = $tenant && $room->lease_end && $today->gt(\Carbon\Carbon::parse($room->lease_end));
        $roomCurrentBalance = $roomBalance['balance'] ?? 0;
        $isOverdue  = $roomCurrentBalance > 0;
        $isPaid     = $roomCurrentBalance <= 0;
        $nextDueDate = $roomBalance['next_due_date'] ?? null;
        $nextDue = $nextDueDate?->format('M d, Y') ?? ($leaseEnded ? 'Lease ended' : '—');
        $renewalEndDate = $tenant ? \Carbon\Carbon::parse($room->lease_end ?? today())->max($today)->addYear()->format('Y-m-d') : null;
    @endphp

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

        {{-- Left Column: Tenant Profile Details + Payment History --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Tenant Profile Details --}}
            @if($tenant)
                <div class="panel p-6">
                    <div class="flex flex-wrap items-start gap-5">
                        <div class="flex h-16 w-16 items-center justify-center rounded-full bg-brand-100 text-brand-700 text-2xl font-bold">
                            {{ strtoupper(substr($tenant->name, 0, 1)) }}
                        </div>
                        <div class="flex-1">
                            <div class="flex flex-wrap items-center justify-between gap-3">
                                <div class="flex flex-wrap items-center gap-3">
                                    <h2 class="text-xl font-bold text-slate-900">{{ $tenant->name }}</h2>
                                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium bg-emerald-50 text-emerald-700">
                                        Active
                                    </span>
                                </div>
                                <div class="flex gap-2">
                                    <a href="{{ route('tenants.edit', $tenant) }}" class="btn btn-secondary py-1.5 text-xs">Edit Tenant</a>
                                    <a href="{{ route('tenants.create', ['property_id' => $property['id'], 'room_id' => $room['id']]) }}" class="btn btn-secondary py-1.5 text-xs">Change Tenant</a>
                                    <form action="{{ route('properties.rooms.vacate', [$property['id'], $building['id'], $room['id']]) }}" method="POST" class="inline m-0">
                                        @csrf
                                        <button type="submit" class="btn btn-secondary border-rose-200 py-1.5 text-xs text-rose-600 hover:text-rose-700 hover:bg-rose-50" onclick="return confirm('Are you sure you want to remove this tenant from this room?');">Remove Tenant</button>
                                    </form>
                                </div>
                            </div>
                            @if($tenant->company)
                                <p class="mt-0.5 text-sm text-slate-500">{{ $tenant->company }}</p>
                            @endif
                            <div class="mt-3 flex flex-wrap gap-4 text-sm text-slate-600">
                                @if($tenant->email)
                                    <span class="flex items-center gap-1.5">
                                        <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-7.5a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" /></svg>
                                        {{ $tenant->email }}
                                    </span>
                                @endif
                                @if($tenant->phone)
                                    <span class="flex items-center gap-1.5">
                                        <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z" /></svg>
                                        {{ $tenant->phone }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="panel p-6">
                    <div class="flex flex-wrap items-center justify-between gap-4">
                        <div class="flex items-center gap-4">
                            <div class="flex h-16 w-16 items-center justify-center rounded-full bg-slate-100 text-slate-400">
                                @include('components.icons.user', ['class' => 'h-8 w-8 text-slate-400'])
                            </div>
                            <div>
                                <h2 class="text-xl font-bold text-slate-900">No Tenant Assigned</h2>
                                <p class="text-sm text-slate-500">This unit is currently vacant and available for rent.</p>
                            </div>
                        </div>
                        <div>
                            <a href="{{ route('tenants.create', ['property_id' => $property['id'], 'room_id' => $room['id']]) }}" class="btn btn-primary py-2 px-4 text-sm font-semibold">
                                Assign Tenant
                            </a>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Payment History --}}
            <div class="panel">
                <div class="flex items-center justify-between border-b border-border px-5 py-4">
                    <h3 class="font-semibold text-slate-900">Payment History</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Property / Unit</th>
                                <th>Due Date</th>
                                <th>Amount</th>
                                <th>Paid Date</th>
                                <th>Method</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($payments as $payment)
                                <tr>
                                    <td>
                                        <p class="font-medium">{{ $property['name'] }}</p>
                                        <p class="text-xs text-slate-500">Unit {{ $room['unit'] }}</p>
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($payment['due_date'])->format('M d, Y') }}</td>
                                    <td class="font-medium">₱{{ number_format($payment['amount']) }}</td>
                                    <td>{{ $payment['paid_date'] ? \Carbon\Carbon::parse($payment['paid_date'])->format('M d, Y') : '—' }}</td>
                                    <td class="text-sm capitalize text-slate-500">{{ $payment['method'] ?? '—' }}</td>
                                    <td><x-status-badge :status="$payment['status']" /></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-10 text-center text-slate-400">
                                        @if($tenant) No payment records found @else No payments — assign a tenant first @endif
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Right Column: Room Details (exactly styled like Leased Units panel) --}}
        <div class="space-y-6">

            <div class="panel p-5 {{ $isOverdue ? 'border-rose-200' : ($isPaid && $tenant ? 'border-emerald-200' : '') }}">
                
                {{-- Card Header --}}
                <div class="flex justify-between items-start mb-3 border-b border-slate-100 pb-3">
                    <div>
                        <h4 class="font-bold text-slate-900">Unit {{ $room->unit }}</h4>
                        <p class="text-xs text-slate-500">{{ $property['name'] }} • {{ $building['name'] }}</p>
                    </div>
                    <div class="flex flex-col items-end gap-2">
                        <div class="flex gap-1">
                            <form action="{{ route('properties.rooms.toggle-maintenance', [$property['id'], $building['id'], $room['id']]) }}" method="POST" class="inline m-0">
                                @csrf
                                @if($room['status'] === 'maintenance')
                                    <button type="submit" class="btn btn-secondary text-emerald-600 hover:text-emerald-700 hover:bg-emerald-50 border-emerald-200 py-1 text-xs"
                                            onclick="return confirm('Restore this room from maintenance?')">
                                        Restore
                                    </button>
                                @else
                                    <button type="submit" class="btn btn-secondary text-amber-600 hover:text-amber-700 hover:bg-amber-50 border-amber-200 py-1 text-xs"
                                            onclick="return confirm('Set this room to under maintenance?')">
                                        Maintenance
                                    </button>
                                @endif
                            </form>
                            <a href="{{ route('properties.rooms.edit', [$property['id'], $building['id'], $room['id']]) }}" class="btn btn-secondary py-1 text-xs">Edit</a>
                        </div>
                        <div class="flex flex-col items-end gap-1">
                            <span class="rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-medium text-emerald-800">
                                {{ ucfirst($displayStatus) }}
                            </span>
                            @if($tenant)
                                @if($isOverdue)
                                    <span class="rounded-full bg-rose-100 px-2.5 py-0.5 text-xs font-medium text-rose-700">⚠ Overdue</span>
                                @elseif($leaseEnded)
                                    <span class="rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-medium text-slate-600">Lease Ended</span>
                                @elseif($isPaid)
                                    <span class="rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-medium text-emerald-700">✓ Paid</span>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Detail Grid --}}
                <div class="mb-3 grid grid-cols-3 gap-2 text-xs">
                    <div class="rounded bg-slate-50 p-2">
                        <p class="text-slate-500">Type</p>
                        <p class="font-medium text-slate-900">{{ $room->type ?? 'N/A' }}</p>
                    </div>
                    <div class="rounded bg-slate-50 p-2">
                        <p class="text-slate-500">Floor</p>
                        <p class="font-medium text-slate-900">{{ $room->floor ?? 'N/A' }}</p>
                    </div>
                    <div class="rounded bg-slate-50 p-2">
                        <p class="text-slate-500">Size</p>
                        <p class="font-medium text-slate-900">{{ $room->size_sqm ?? 0 }} sqm</p>
                    </div>
                </div>

                {{-- Financial Details Breakdown --}}
                <dl class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <dt class="text-slate-500">Lease Start</dt>
                        <dd class="font-medium text-slate-900">{{ $room->lease_start ? \Carbon\Carbon::parse($room->lease_start)->format('M d, Y') : '—' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-slate-500">Lease End</dt>
                        <dd class="font-medium text-slate-900">{{ $room->lease_end ? \Carbon\Carbon::parse($room->lease_end)->format('M d, Y') : '—' }}</dd>
                    </div>
                    @if($tenant && $latestPayment)
                        <div class="flex justify-between items-center border-t border-slate-100 pt-2">
                            <dt class="text-slate-500">Last Due</dt>
                            <dd class="flex items-center gap-1.5 text-right">
                                @if($isPaid)
                                    <span class="text-xs font-semibold text-emerald-600">✓ Paid</span>
                                @elseif($isOverdue)
                                    <span class="text-xs font-semibold text-rose-600">⚠ Overdue</span>
                                @else
                                    <span class="text-xs font-semibold text-amber-600">⏳ Pending</span>
                                @endif
                                <span class="font-medium {{ $isOverdue ? 'text-rose-600' : 'text-slate-900' }}">{{ \Carbon\Carbon::parse($latestPayment->due_date)->format('M d, Y') }}</span>
                            </dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-slate-500">Next Due</dt>
                            <dd class="font-medium text-slate-900">{{ $nextDue }}</dd>
                        </div>
                    @endif
                    <div class="flex justify-between font-semibold mt-2 pt-2 border-t border-slate-100">
                        <dt class="text-slate-700">Monthly Rent</dt>
                        <dd class="text-brand-700">₱{{ number_format($room->rent) }}</dd>
                    </div>
                    @if($tenant)
                        <div class="flex justify-between">
                            <dt class="text-slate-500">Downpayment</dt>
                            <dd class="font-medium text-slate-900">{{ $roomBalance['downpayment_months'] ?? 0 }} mo. / ₱{{ number_format($roomBalance['downpayment_amount'] ?? 0) }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-slate-500">Months Payable</dt>
                            <dd class="font-medium text-slate-900">{{ $roomBalance['monthly_months_due'] ?? 0 }} mo.</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-slate-500">Months Behind</dt>
                            <dd class="font-medium text-slate-900">{{ $roomBalance['months_behind'] ?? 0 }} mo.</dd>
                        </div>
                        @if(($roomBalance['advance'] ?? 0) > 0)
                            <div class="flex justify-between">
                                <dt class="text-slate-500">Advance Credit</dt>
                                <dd class="font-medium text-emerald-600">₱{{ number_format($roomBalance['advance']) }}</dd>
                            </div>
                        @endif
                        <div class="flex justify-between font-semibold">
                            <dt class="text-slate-700">Current Balance</dt>
                            <dd class="{{ $roomCurrentBalance > 0 ? 'text-rose-600' : 'text-emerald-600' }}">₱{{ number_format($roomCurrentBalance) }}</dd>
                        </div>
                    @else
                        <div class="flex justify-between font-semibold">
                            <dt class="text-slate-700">Current Balance</dt>
                            <dd class="font-medium text-slate-400">—</dd>
                        </div>
                    @endif
                </dl>

                {{-- Record Payment / Pay Now --}}
                @if($tenant)
                    <div class="mt-4">
                        @if($leaseEnded && $roomCurrentBalance <= 0)
                            <span class="btn btn-secondary w-full py-1.5 text-xs text-center justify-center opacity-70">No Payment Required</span>
                        @else
                            <a href="{{ route('payments.create', ['tenant_id' => $tenant->id, 'room_id' => $room->id]) }}"
                               class="btn {{ $isOverdue ? 'bg-rose-600 text-white hover:bg-rose-700 border-rose-600' : 'btn-primary' }} w-full py-1.5 text-xs text-center justify-center">
                                {{ $isOverdue ? '⚠ Pay Now' : 'Record Payment' }}
                            </a>
                        @endif
                    </div>
                @endif

                {{-- Lease Renewal --}}
                @if($tenant && $leaseEnded)
                    <form method="POST" action="{{ route('tenants.rooms.renew', [$tenant, $room]) }}" class="mt-3 flex gap-2">
                        @csrf
                        <input type="date" name="lease_end" value="{{ old('lease_end', $renewalEndDate) }}"
                               min="{{ $today->copy()->addDay()->format('Y-m-d') }}"
                               class="input-field flex-1 py-1.5 text-xs" required>
                        <button type="submit" class="btn btn-primary py-1.5 text-xs">Renew Lease</button>
                    </form>
                    @error('lease_end')
                        <p class="mt-2 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                @endif

                {{-- Rental Contract --}}
                <div class="mt-4 pt-4 border-t border-slate-100">
                    <p class="text-xs font-semibold text-slate-700 mb-2">Rental Contract</p>
                    @if($tenant)
                        @if($tenant->contract_path)
                            <div class="flex items-center justify-between rounded-lg border border-border p-2 mb-2">
                                <div class="flex items-center gap-2">
                                    <svg class="h-5 w-5 text-brand-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" /></svg>
                                    <span class="text-xs font-medium text-slate-900 truncate">{{ $tenant->contract_name ?? 'Contract Document' }}</span>
                                </div>
                                <a href="{{ route('tenants.contract.download', $tenant) }}" target="_blank" class="text-brand-600 hover:text-brand-700">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" /></svg>
                                </a>
                            </div>
                        @endif
                        <form method="POST" action="{{ route('tenants.contract.upload', $tenant) }}" enctype="multipart/form-data" class="flex gap-2">
                            @csrf
                            <input type="file" name="contract" accept=".pdf,.doc,.docx"
                                   class="block w-full text-xs text-slate-500 file:mr-2 file:rounded-md file:border-0 file:bg-brand-50 file:px-2 file:py-1 file:text-xs file:font-semibold file:text-brand-700 hover:file:bg-brand-100" required>
                            <button type="submit" class="btn btn-secondary py-1.5 text-xs">{{ $tenant->contract_path ? 'Re-upload' : 'Upload' }}</button>
                        </form>
                    @else
                        <p class="text-xs text-slate-400">No contract on file. Assign a tenant to create a lease.</p>
                    @endif
                </div>

            </div>

        </div>
    </div>
@endsection
