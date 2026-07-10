@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
    {{-- Overdue Alert Banner --}}
    @if($stats['overdue_count'] > 0)
        <div data-alert class="mb-6 flex flex-col gap-3 rounded-xl border border-rose-200 bg-rose-50 p-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-start gap-3">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-rose-100 text-rose-600">
                    @include('components.icons.alert', ['class' => 'h-5 w-5'])
                </div>
                <div>
                    <p class="font-semibold text-rose-900">{{ $stats['overdue_count'] }} overdue payment{{ $stats['overdue_count'] > 1 ? 's' : '' }}</p>
                    <p class="text-sm text-rose-700">Total outstanding: ₱{{ number_format($stats['overdue_amount']) }} — action required</p>
                </div>
            </div>
            <a href="{{ route('payments.index', ['status' => 'overdue']) }}" class="btn btn-primary shrink-0 bg-rose-600 hover:bg-rose-700 focus:ring-rose-500">Review Overdue</a>
        </div>
    @endif

    {{-- KPI Stats --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-stat-card label="Total Properties" :value="$stats['total_properties']" change="+1 this quarter" change-type="up" icon="property" color="brand" />
        <x-stat-card label="Occupancy Rate" :value="$stats['occupancy_rate'] . '%'" change="+2.3% vs last month" change-type="up" icon="building" color="emerald" />
        <x-stat-card label="Monthly Revenue" :value="'₱' . number_format($stats['monthly_revenue'])" change="82.8% collected" change-type="neutral" icon="revenue" color="sky" />
        <x-stat-card label="Overdue Amount" :value="'₱' . number_format($stats['overdue_amount'])" change="{{ $stats['overdue_count'] }} accounts" change-type="down" icon="alert" color="rose" />
    </div>

    {{-- Charts Row --}}
    <div class="mt-6">
        {{-- Revenue Chart (full width) --}}
        <div class="panel">
            <div class="flex items-center justify-between border-b border-border px-5 py-4">
                <div>
                    <h3 class="font-semibold text-slate-900">Revenue Overview</h3>
                    <p class="text-xs text-slate-500">Collected vs expected — last 6 months</p>
                </div>
                <select class="input-field w-auto py-1.5 text-xs">
                    <option>Last 6 months</option>
                    <option>Last 12 months</option>
                </select>
            </div>
            <div class="p-5">
                <div class="rounded-2xl border border-border bg-slate-50 p-4">
                    <canvas id="revenueChart" height="100"></canvas>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
    <script>
        const revenueData = @json($revenueChart);
        const revenueCtx = document.getElementById('revenueChart');

        if (revenueCtx) {
            new Chart(revenueCtx, {
                type: 'line',
                data: {
                    labels: revenueData.map((entry) => entry.month),
                    datasets: [
                        {
                            label: 'Collected',
                            data: revenueData.map((entry) => entry.collected),
                            borderColor: '#2563eb',
                            backgroundColor: 'rgba(37, 99, 235, 0.15)',
                            fill: true,
                            tension: 0.35,
                        },
                        {
                            label: 'Expected',
                            data: revenueData.map((entry) => entry.expected),
                            borderColor: '#94a3b8',
                            backgroundColor: 'rgba(148, 163, 184, 0.10)',
                            fill: true,
                            tension: 0.35,
                        },
                    ],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom' },
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: (value) => '₱' + Number(value).toLocaleString(),
                            },
                        },
                    },
                },
            });
        }
    </script>
@endpush
