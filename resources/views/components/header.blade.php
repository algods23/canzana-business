<header class="sticky top-0 z-30 border-b border-border bg-surface/80 backdrop-blur-md">
    <div class="flex h-16 items-center justify-between gap-4 px-4 sm:px-6 lg:px-8">
        <div class="flex items-center gap-3">
            @empty($hideSidebar)
                <button id="sidebar-open" type="button" class="rounded-lg p-2 text-slate-600 hover:bg-slate-100 lg:hidden">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" /></svg>
                </button>
            @endempty
            <button type="button" onclick="history.back()" class="rounded-lg p-2 text-slate-600 hover:bg-slate-100" title="Go back">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" /></svg>
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
            @auth
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="flex items-center gap-2 rounded-lg border border-border px-3 py-2 text-sm font-medium text-slate-600 hover:bg-slate-100 hover:text-rose-600 hover:border-rose-200">
                        <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15M12 9l-3 3m0 0 3 3m-3-3h12.75" /></svg>
                        <span class="hidden sm:inline">Logout</span>
                    </button>
                </form>
            @endauth

            @yield('header-actions')
        </div>
    </div>
</header>
