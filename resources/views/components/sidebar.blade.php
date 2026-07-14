@php
    $navItems = [
        ['route' => 'monitoring.rental', 'label' => 'Rental Monitoring', 'icon' => 'payment'],
        ['route' => 'monitoring.agriculture', 'label' => 'Agriculture Monitoring', 'icon' => 'expense'],
        ['route' => 'monitoring.tilapia', 'label' => 'Tilapia Monitoring', 'icon' => 'expense'],
    ];

    $monitoringItems = [
        
        
        ['route' => 'monitoring.conel', 'label' => 'Conel Monitoring', 'icon' => 'payment'],
        ['route' => 'monitoring.128', 'label' => '128 Monitoring', 'icon' => 'payment'],
    ];
@endphp

<aside id="sidebar" class="fixed inset-y-0 left-0 z-50 flex w-64 -translate-x-full flex-col bg-sidebar transition-transform duration-300 lg:translate-x-0">
    <div class="flex h-16 items-center justify-between border-b border-slate-700/50 px-5">
        <a href="{{ route('monitoring.rental') }}" class="flex items-center gap-2.5">
            <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-brand-600">
                <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                </svg>
            </div>
            <div>
                <span class="block text-sm font-bold text-white">Canzana</span>
                <span class="block text-[10px] font-medium uppercase tracking-widest text-brand-400">Business</span>
            </div>
        </a>
        <button id="sidebar-close" type="button" class="rounded-lg p-1.5 text-slate-400 hover:bg-sidebar-hover hover:text-white lg:hidden">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg>
        </button>
    </div>

    <nav class="flex-1 space-y-1 overflow-y-auto px-3 py-4 scrollbar-thin">
        <p class="mb-2 px-3 text-[10px] font-semibold uppercase tracking-widest text-slate-500">Main Menu</p>
        @foreach($navItems as $item)
            @php $active = request()->routeIs($item['route'] . '*') || request()->routeIs($item['route']); @endphp
            <a href="{{ route($item['route']) }}" class="sidebar-link {{ $active ? 'sidebar-link-active' : '' }}">
                @include('components.icons.' . $item['icon'], ['class' => 'h-5 w-5 shrink-0'])
                {{ $item['label'] }}
            </a>
        @endforeach

        <p class="mb-2 mt-6 px-3 text-[10px] font-semibold uppercase tracking-widest text-slate-500">Monitoring</p>
        @foreach($monitoringItems as $item)
            @php $active = request()->routeIs($item['route'] . '*') || request()->routeIs($item['route']); @endphp
            <a href="{{ route($item['route']) }}" class="sidebar-link {{ $active ? 'sidebar-link-active' : '' }}">
                @include('components.icons.' . $item['icon'], ['class' => 'h-5 w-5 shrink-0'])
                {{ $item['label'] }}
            </a>
        @endforeach


    </nav>

    <div class="border-t border-slate-700/50 p-4">
        @auth
            <div class="flex items-center gap-3">
                <div class="flex h-9 w-9 items-center justify-center rounded-full bg-brand-700 text-sm font-semibold text-white">{{ strtoupper(substr(auth()->user()->name, 0, 2)) }}</div>
                <div class="min-w-0 flex-1">
                    <p class="truncate text-sm font-medium text-white">{{ auth()->user()->name }}</p>
                    <p class="truncate text-xs text-slate-400">{{ ucfirst(auth()->user()->role) }}</p>
                </div>
            </div>
        @endauth
    </div>
</aside>
