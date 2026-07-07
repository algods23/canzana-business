@extends('layouts.app')

@section('title', $tenant['name'])
@section('page-title', $tenant['name'])

@section('breadcrumb')
    <a href="{{ route('tenants.index') }}" class="hover:text-brand-600">Tenants</a>
    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" /></svg>
    <span class="text-slate-700">{{ $tenant['name'] }}</span>
@endsection

@section('header-actions')
    <a href="{{ route('tenants.edit', $tenant) }}" class="btn btn-secondary">Edit Tenant</a>
    <a href="{{ route('payments.create') }}" class="btn btn-primary">Record Payment</a>
@endsection

@section('content')
    @php $displayStatus = $totalBalance > 0 ? 'overdue' : $tenant['status']; @endphp
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="lg:col-span-2 space-y-6">
            {{-- Profile Card --}}
            <div class="panel p-6">
                <div class="flex flex-wrap items-start gap-5">
                    <div class="flex h-16 w-16 items-center justify-center rounded-full {{ $displayStatus === 'overdue' ? 'bg-rose-100 text-rose-700' : 'bg-brand-100 text-brand-700' }} text-2xl font-bold">
                        {{ strtoupper(substr($tenant['name'], 0, 1)) }}
                    </div>
                    <div class="flex-1">
                        <div class="flex flex-wrap items-center gap-3">
                            <h2 class="text-xl font-bold text-slate-900">{{ $tenant['name'] }}</h2>
                            <x-status-badge :status="$displayStatus" />
                        </div>
                        @if($tenant['company'])
                            <p class="mt-0.5 text-sm text-slate-500">{{ $tenant['company'] }}</p>
                        @endif
                        <div class="mt-3 flex flex-wrap gap-4 text-sm text-slate-600">
                            <span class="flex items-center gap-1.5">
                                <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-7.5a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" /></svg>
                                {{ $tenant['email'] }}
                            </span>
                            <span class="flex items-center gap-1.5">
                                <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z" /></svg>
                                {{ $tenant['phone'] }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Payment History --}}
            <div class="panel">
                <div class="border-b border-border px-5 py-4">
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
                                        <p class="font-medium">{{ $payment['property'] }}</p>
                                        <p class="text-xs text-slate-500">Unit {{ $payment['unit'] }}</p>
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($payment['due_date'])->format('M d, Y') }}</td>
                                    <td class="font-medium">₱{{ number_format($payment['amount']) }}</td>
                                    <td>{{ $payment['paid_date'] ? \Carbon\Carbon::parse($payment['paid_date'])->format('M d, Y') : '—' }}</td>
                                    <td class="text-sm capitalize text-slate-500">{{ $payment['method'] ?: '—' }}</td>
                                    <td><x-status-badge :status="$payment['status']" /></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-8 text-center text-slate-500">No payment records found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">
            @if($tenant->rooms->count() > 0)
                <div class="space-y-4">
                    <h3 class="font-semibold text-slate-900">Leased Units ({{ $tenant->rooms->count() }})</h3>
                    @foreach($tenant->rooms as $room)
                        @php
                            $roomBalance = $roomBalances->first(fn ($breakdown) => $breakdown['room']->id === $room->id);
                            $latestPayment = $latestPaymentByRoom[$room->id] ?? null;
                            $today = \Carbon\Carbon::today();

                            // Calculate next due date
                            $nextDueDate = null;
                            if ($latestPayment?->due_date) {
                                $nextDueDate = \Carbon\Carbon::parse($latestPayment->due_date)->addMonth();
                            } elseif ($room->lease_start) {
                                $nextDueDate = \Carbon\Carbon::parse($room->lease_start)->addMonth();
                            }

                            // If the last payment was paid BUT the next due date has arrived → new cycle is pending
                            $newCycleDue = $latestPayment && $latestPayment->status === 'paid' && $nextDueDate && $today->gte($nextDueDate);

                            $isPaid     = $latestPayment && $latestPayment->status === 'paid' && !$newCycleDue;
                            $isOverdue  = ($latestPayment && $latestPayment->status === 'overdue') || ($newCycleDue && $today->gt($nextDueDate));
                            $hasPending = ($latestPayment && $latestPayment->status === 'pending') || $newCycleDue;

                            $nextDue = $nextDueDate?->format('M d, Y');
                        @endphp
                        <div class="panel p-5 {{ $isOverdue ? 'border-rose-200' : ($isPaid ? 'border-emerald-200' : '') }}">
                            <div class="flex justify-between items-start mb-3 border-b border-slate-100 pb-3">
                                <div>
                                    <h4 class="font-bold text-slate-900">Unit {{ $room->unit }}</h4>
                                    <p class="text-xs text-slate-500">{{ $room->buildingModel?->propertyModel?->name }} • {{ $room->buildingModel?->name }}</p>
                                </div>
                                <div class="flex flex-col items-end gap-1">
                                    <span class="rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-medium text-emerald-800">Active</span>
                                    @if($isOverdue)
                                        <span class="rounded-full bg-rose-100 px-2.5 py-0.5 text-xs font-medium text-rose-700">⚠ Overdue</span>
                                    @elseif($isPaid)
                                        <span class="rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-medium text-emerald-700">✓ Paid</span>
                                    @elseif($hasPending)
                                        <span class="rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-medium text-amber-700">Pending</span>
                                    @else
                                        <span class="rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-medium text-slate-500">No Payments</span>
                                    @endif
                                </div>
                            </div>
                            <dl class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <dt class="text-slate-500">Lease Start</dt>
                                    <dd class="font-medium text-slate-900">{{ $room->lease_start ? \Carbon\Carbon::parse($room->lease_start)->format('M d, Y') : '—' }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-slate-500">Lease End</dt>
                                    <dd class="font-medium text-slate-900">{{ $room->lease_end ? \Carbon\Carbon::parse($room->lease_end)->format('M d, Y') : '—' }}</dd>
                                </div>
                                @if($latestPayment)
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
                                <div class="flex justify-between font-semibold mt-2 pt-2 border-t border-slate-50">
                                    <dt class="text-slate-700">Monthly Rent</dt>
                                    <dd class="text-brand-700">₱{{ number_format($room->rent) }}</dd>
                                </div>
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
                                    <dd class="{{ ($roomBalance['balance'] ?? 0) > 0 ? 'text-rose-600' : 'text-emerald-600' }}">₱{{ number_format($roomBalance['balance'] ?? 0) }}</dd>
                                </div>
                            </dl>
                            <div class="mt-4 flex gap-2">
                                <a href="{{ route('payments.create', ['tenant_id' => $tenant->id, 'room_id' => $room->id]) }}" class="btn {{ $isOverdue ? 'bg-rose-600 text-white hover:bg-rose-700 border-rose-600' : 'btn-primary' }} flex-1 py-1.5 text-xs text-center justify-center">{{ $isOverdue ? '⚠ Pay Now' : 'Record Payment' }}</a>
                                <a href="{{ route('properties.room', [$room->buildingModel->property_id, $room->building_id, $room->id]) }}" class="btn btn-secondary flex-1 py-1.5 text-xs text-center justify-center">View Unit</a>
                            </div>
                        </div>
                    @endforeach
                    <div class="panel p-5 bg-brand-50 border-brand-100">
                        <div class="flex justify-between font-bold text-base">
                            <span class="text-brand-900">Total Monthly Payable</span>
                            <span class="text-brand-700">₱{{ number_format($tenant->rooms->sum('rent')) }}</span>
                        </div>
                        <div class="mt-2 flex justify-between text-sm font-semibold">
                            <span class="text-brand-900">Total Current Balance</span>
                            <span class="{{ $totalBalance > 0 ? 'text-rose-600' : 'text-emerald-600' }}">₱{{ number_format($totalBalance) }}</span>
                        </div>
                    </div>
                </div>
            @else
                <div class="panel p-6 text-center text-slate-500">
                    No units assigned to this tenant.
                </div>
            @endif

            @if($totalBalance > 0)
                <div class="rounded-xl border border-rose-200 bg-rose-50 p-5">
                    <div class="flex items-center gap-2 text-rose-800">
                        @include('components.icons.alert', ['class' => 'h-5 w-5'])
                        <span class="font-semibold">Payment Overdue</span>
                    </div>
                    <p class="mt-2 text-sm text-rose-700">This tenant has an outstanding balance of ₱{{ number_format($totalBalance) }}. Send a reminder or record a partial payment.</p>
                    <button type="button" class="btn mt-4 w-full bg-rose-600 text-white hover:bg-rose-700">Send Reminder</button>
                </div>
            @endif

            <div class="panel">
                <div class="border-b border-border px-5 py-4">
                    <h3 class="font-semibold text-slate-900">Recent Activity</h3>
                </div>
                <div class="divide-y divide-border">
                    @foreach($activities as $activity)
                        <div class="px-5 py-3">
                            <p class="text-sm font-medium text-slate-900">{{ $activity['title'] }}</p>
                            <p class="text-xs text-slate-500">{{ $activity['time'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection

