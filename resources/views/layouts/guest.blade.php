<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Access') — Canzana Business</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-950 text-slate-100">
    <div class="grid min-h-screen lg:grid-cols-2">
        <div class="relative hidden overflow-hidden bg-[radial-gradient(circle_at_top_left,_rgba(59,130,246,0.35),_transparent_35%),linear-gradient(135deg,_#0f172a,_#111827_55%,_#020617)] lg:flex lg:flex-col lg:justify-between">
            <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width="80" height="80" viewBox="0 0 80 80" xmlns="http://www.w3.org/2000/svg"%3E%3Cg fill="none" fill-rule="evenodd"%3E%3Cg fill="%23ffffff" fill-opacity="0.04"%3E%3Cpath d="M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')] opacity-70"></div>
            <div class="relative z-10 p-10 xl:p-16">
                <div class="flex items-center gap-3">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-brand-500 shadow-lg shadow-brand-500/30">
                        <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" /></svg>
                    </div>
                    <div>
                        <p class="text-lg font-semibold text-white">Canzana Business</p>
                        <p class="text-sm text-slate-300">Property operations dashboard</p>
                    </div>
                </div>

                <div class="mt-16 max-w-xl space-y-6">
                    <h1 class="text-4xl font-bold tracking-tight text-white xl:text-5xl">Track buildings, rooms, tenants, and payments from one real system.</h1>
                    <p class="text-lg text-slate-300">Authentication, role-aware access, live charts, and database-backed records are all wired into the same app shell.</p>
                </div>
            </div>

            <div class="relative z-10 grid gap-4 p-10 sm:grid-cols-3 xl:p-16">
                <div class="rounded-2xl border border-white/10 bg-white/5 p-4 backdrop-blur">
                    <p class="text-xs uppercase tracking-[0.3em] text-slate-400">Live data</p>
                    <p class="mt-2 text-2xl font-semibold text-white">Eloquent</p>
                </div>
                <div class="rounded-2xl border border-white/10 bg-white/5 p-4 backdrop-blur">
                    <p class="text-xs uppercase tracking-[0.3em] text-slate-400">Charts</p>
                    <p class="mt-2 text-2xl font-semibold text-white">Chart.js</p>
                </div>
                <div class="rounded-2xl border border-white/10 bg-white/5 p-4 backdrop-blur">
                    <p class="text-xs uppercase tracking-[0.3em] text-slate-400">Access</p>
                    <p class="mt-2 text-2xl font-semibold text-white">Role-based</p>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-center bg-slate-50 px-6 py-12 text-slate-900">
            <div class="w-full max-w-md">
                @yield('content')
            </div>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
