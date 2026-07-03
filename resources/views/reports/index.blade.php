@extends('layouts.app')

@section('title', 'Reports')
@section('page-title', 'Reports & Analytics')

@section('header-actions')
    <button type="button" class="btn btn-secondary">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" /></svg>
        Export PDF
    </button>
    <button type="button" class="btn btn-primary">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" /></svg>
        Export CSV
    </button>
@endsection

@section('content')
    {{-- Report Type Cards --}}
    <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        @foreach([
            ['title' => 'Occupancy Report', 'desc' => 'Room occupancy by property', 'icon' => 'building', 'iconClass' => 'bg-brand-50 text-brand-700'],
            ['title' => 'Revenue Report', 'desc' => 'Monthly collection summary', 'icon' => 'revenue', 'iconClass' => 'bg-emerald-50 text-emerald-700'],
            ['title' => 'Overdue Report', 'desc' => 'Outstanding payments', 'icon' => 'alert', 'iconClass' => 'bg-rose-50 text-rose-700'],
            ['title' => 'Tenant Report', 'desc' => 'Active leases & contracts', 'icon' => 'users', 'iconClass' => 'bg-sky-50 text-sky-700'],
        ] as $report)
            <button type="button" class="panel p-5 text-left transition-all hover:border-brand-300 hover:shadow-md">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg {{ $report['iconClass'] }}">
                    @include('components.icons.' . $report['icon'], ['class' => 'h-5 w-5'])
                </div>
                <h3 class="mt-3 font-semibold text-slate-900">{{ $report['title'] }}</h3>
                <p class="mt-1 text-xs text-slate-500">{{ $report['desc'] }}</p>
            </button>
        @endforeach
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        {{-- Revenue Summary --}}
        <div class="panel">
            <div class="border-b border-border px-5 py-4">
                <h3 class="font-semibold text-slate-900">Revenue Summary</h3>
                <p class="text-xs text-slate-500">January — July 2025</p>
            </div>
            <div class="p-5">
                <div class="mb-4 grid grid-cols-2 gap-4">
                    <div class="rounded-lg bg-brand-50 p-4">
                        <p class="text-xs font-medium text-brand-600">Expected Revenue</p>
                        <p class="mt-1 text-xl font-bold text-brand-900">₱{{ number_format($stats['monthly_revenue']) }}</p>
                    </div>
                    <div class="rounded-lg bg-emerald-50 p-4">
                        <p class="text-xs font-medium text-emerald-600">Collected This Month</p>
                        <p class="mt-1 text-xl font-bold text-emerald-900">₱{{ number_format($stats['collected_this_month']) }}</p>
                    </div>
                </div>
                <div class="rounded-2xl border border-border bg-slate-50 p-4">
                    <canvas id="reportRevenueChart" height="140"></canvas>
                </div>
            </div>
        </div>

        {{-- Occupancy Breakdown --}}
        <div class="panel">
            <div class="border-b border-border px-5 py-4">
                <h3 class="font-semibold text-slate-900">Occupancy Breakdown</h3>
            </div>
            <div class="p-5">
                <div class="rounded-2xl border border-border bg-slate-50 p-4">
                    <canvas id="reportOccupancyChart" height="220"></canvas>
                </div>
            </div>
        </div>

        {{-- Overdue Accounts --}}
        <div class="panel lg:col-span-2">
            <div class="border-b border-border px-5 py-4">
                <h3 class="font-semibold text-slate-900">Overdue Accounts</h3>
                <p class="text-xs text-slate-500">{{ count($overduePayments) }} overdue payment records</p>
            </div>
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Tenant</th>
                            <th>Property / Unit</th>
                            <th>Amount</th>
                            <th>Due Date</th>
                            <th>Days Overdue</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($overduePayments as $payment)
                            @php $daysOverdue = now()->diffInDays(\Carbon\Carbon::parse($payment['due_date'])); @endphp
                            <tr>
                                <td class="font-medium">{{ $payment['tenant'] }}</td>
                                <td>{{ $payment['property'] }} · {{ $payment['unit'] }}</td>
                                <td class="font-semibold text-rose-600">₱{{ number_format($payment['amount']) }}</td>
                                <td>{{ \Carbon\Carbon::parse($payment['due_date'])->format('M d, Y') }}</td>
                                <td><span class="badge badge-danger">{{ $daysOverdue }} days</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
    <script>
        const reportRevenue = @json($revenueChart);
        const reportRevenueCtx = document.getElementById('reportRevenueChart');

        if (reportRevenueCtx) {
            new Chart(reportRevenueCtx, {
                type: 'bar',
                data: {
                    labels: reportRevenue.map((entry) => entry.month),
                    datasets: [
                        {
                            label: 'Collected',
                            data: reportRevenue.map((entry) => entry.collected),
                            backgroundColor: '#2563eb',
                        },
                        {
                            label: 'Expected',
                            data: reportRevenue.map((entry) => entry.expected),
                            backgroundColor: '#94a3b8',
                        },
                    ],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: (value) => '₱' + Number(value).toLocaleString(),
                            },
                        },
                    },
                    plugins: {
                        legend: { position: 'bottom' },
                    },
                },
            });
        }

        const reportOccupancyCtx = document.getElementById('reportOccupancyChart');
        const reportOccupancy = @json($properties->map(fn ($property) => ['name' => $property['name'], 'rate' => $property['occupancy_rate']])->values());

        if (reportOccupancyCtx) {
            new Chart(reportOccupancyCtx, {
                type: 'polarArea',
                data: {
                    labels: reportOccupancy.map((entry) => entry.name),
                    datasets: [{
                        data: reportOccupancy.map((entry) => entry.rate),
                        backgroundColor: ['#2563eb', '#0f766e', '#ea580c', '#be123c', '#7c3aed'],
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom' },
                    },
                },
            });
        }
    </script>
@endpush
