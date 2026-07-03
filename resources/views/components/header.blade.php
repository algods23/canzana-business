<header class="sticky top-0 z-30 border-b border-border bg-surface/80 backdrop-blur-md">
    <div class="flex h-16 items-center justify-between gap-4 px-4 sm:px-6 lg:px-8">
        <div class="flex items-center gap-3">
            <button id="sidebar-open" type="button" class="rounded-lg p-2 text-slate-600 hover:bg-slate-100 lg:hidden">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" /></svg>
            </button>
            <div>
                <h1 class="text-lg font-semibold text-slate-900 sm:text-xl">@yield('page-title', 'Dashboard')</h1>
                @hasSection('breadcrumb')
                    <nav class="mt-0.5 flex items-center gap-1.5 text-xs text-slate-500">
                        @yield('breadcrumb')
                    </nav>
                @endif
            </div>
        </div>

        <div class="flex items-center gap-2 sm:gap-3">
            <div class="relative hidden sm:block">
                <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" /></svg>
                <input type="search" placeholder="Search properties, tenants, units..." class="input-field w-64 pl-9 lg:w-80">
            </div>

            @auth
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="flex items-center gap-2 rounded-lg border border-border px-3 py-2 text-sm font-medium text-slate-600 hover:bg-slate-100">
                        <span class="hidden sm:inline">{{ auth()->user()->name }}</span>
                        <span class="rounded-full bg-brand-50 px-2 py-0.5 text-[10px] font-semibold uppercase tracking-widest text-brand-700">{{ auth()->user()->role }}</span>
                    </button>
                </form>
            @endauth

            @yield('header-actions')
        </div>
    </div>
</header>
