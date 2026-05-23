@php
    $links = [
        ['label' => 'Dashboard', 'route' => 'dashboard', 'pattern' => 'dashboard', 'icon' => '⌂'],
        ['label' => 'Produk', 'route' => 'products.index', 'pattern' => 'products.*', 'icon' => '📦'],
        ['label' => 'Kategori', 'route' => 'categories.index', 'pattern' => 'categories.*', 'icon' => '🏷️'],
        ['label' => 'Supplier', 'route' => 'suppliers.index', 'pattern' => 'suppliers.*', 'icon' => '🚚'],
        ['label' => 'Stok', 'route' => 'stock-movements.index', 'pattern' => 'stock-movements.*', 'icon' => '⇄'],
        ['label' => 'Penjualan', 'route' => 'sales.index', 'pattern' => 'sales.*', 'icon' => '🧾'],
        ['label' => 'Laporan', 'route' => 'reports.index', 'pattern' => 'reports.*', 'icon' => '📊'],
        ['label' => 'Pengaturan Toko', 'route' => 'settings.store.edit', 'pattern' => 'settings.*', 'icon' => '⚙️'],
    ];
@endphp

<aside class="hidden w-72 shrink-0 border-r border-slate-200 bg-white transition-colors duration-300 dark:border-slate-800 dark:bg-slate-950 lg:fixed lg:inset-y-0 lg:flex lg:flex-col">
    <div class="flex h-20 items-center gap-3 border-b border-slate-200 px-6 dark:border-slate-800">
        <a href="{{ route('dashboard') }}" class="flex min-w-0 items-center gap-3">
            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-indigo-600 to-sky-500 text-sm font-black text-white shadow-lg shadow-indigo-600/25">INV</div>
            <div class="min-w-0">
                <p class="truncate text-sm font-black text-slate-950 dark:text-white">Inventory UMKM</p>
                <p class="text-xs font-medium text-slate-500 dark:text-slate-400">Sales & Stock</p>
            </div>
        </a>
    </div>

    <nav class="flex-1 space-y-1 overflow-y-auto px-4 py-6">
        @foreach ($links as $link)
            @php
                $href = $link['route'] ? route($link['route']) : '#';
                $active = $link['pattern'] ? request()->routeIs($link['pattern']) : false;
            @endphp
            <x-admin.nav-link :href="$href" :active="$active" :icon="$link['icon']">
                {{ $link['label'] }}
            </x-admin.nav-link>
        @endforeach
    </nav>

    <div class="border-t border-slate-200 p-4 dark:border-slate-800">
        <div class="rounded-2xl bg-slate-50 p-4 dark:bg-slate-900">
            <p class="text-sm font-bold text-slate-900 dark:text-white">{{ auth()->user()->name ?? 'Admin' }}</p>
            <p class="mt-1 truncate text-xs text-slate-500 dark:text-slate-400">{{ auth()->user()->email ?? '' }}</p>
            <form method="POST" action="{{ route('logout') }}" class="mt-4">
                @csrf
                <button type="submit" class="w-full rounded-xl bg-slate-950 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-800 focus:outline-none focus:ring-4 focus:ring-slate-300 dark:bg-white dark:text-slate-950 dark:hover:bg-slate-200 dark:focus:ring-slate-700">
                    Logout
                </button>
            </form>
        </div>
    </div>
</aside>

<div id="mobile-sidebar" class="pointer-events-none fixed inset-0 z-50 opacity-0 transition-opacity duration-300 lg:hidden" aria-hidden="true">
    <button type="button" class="absolute inset-0 bg-slate-950/50 backdrop-blur-sm" data-close-sidebar aria-label="Tutup menu"></button>
    <aside id="mobile-sidebar-panel" class="relative flex h-full w-80 max-w-[85vw] -translate-x-full flex-col border-r border-slate-200 bg-white shadow-2xl transition-transform duration-300 dark:border-slate-800 dark:bg-slate-950">
        <div class="flex h-20 items-center justify-between gap-3 border-b border-slate-200 px-6 dark:border-slate-800">
            <a href="{{ route('dashboard') }}" class="flex min-w-0 items-center gap-3" data-close-sidebar>
                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-indigo-600 to-sky-500 text-sm font-black text-white shadow-lg shadow-indigo-600/25">INV</div>
                <div class="min-w-0">
                    <p class="truncate text-sm font-black text-slate-950 dark:text-white">Inventory UMKM</p>
                    <p class="text-xs font-medium text-slate-500 dark:text-slate-400">Sales & Stock</p>
                </div>
            </a>

            <button type="button" class="rounded-xl p-2 text-slate-500 transition hover:bg-slate-100 hover:text-slate-900 dark:text-slate-400 dark:hover:bg-slate-900 dark:hover:text-white" data-close-sidebar aria-label="Tutup menu">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <nav class="flex-1 space-y-1 overflow-y-auto px-4 py-6">
            @foreach ($links as $link)
                @php
                    $href = $link['route'] ? route($link['route']) : '#';
                    $active = $link['pattern'] ? request()->routeIs($link['pattern']) : false;
                @endphp
                <x-admin.nav-link :href="$href" :active="$active" :icon="$link['icon']" data-close-sidebar>
                    {{ $link['label'] }}
                </x-admin.nav-link>
            @endforeach
        </nav>

        <div class="border-t border-slate-200 p-4 dark:border-slate-800">
            <div class="rounded-2xl bg-slate-50 p-4 dark:bg-slate-900">
                <p class="text-sm font-bold text-slate-900 dark:text-white">{{ auth()->user()->name ?? 'Admin' }}</p>
                <p class="mt-1 truncate text-xs text-slate-500 dark:text-slate-400">{{ auth()->user()->email ?? '' }}</p>
                <form method="POST" action="{{ route('logout') }}" class="mt-4">
                    @csrf
                    <button type="submit" class="w-full rounded-xl bg-slate-950 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-800 focus:outline-none focus:ring-4 focus:ring-slate-300 dark:bg-white dark:text-slate-950 dark:hover:bg-slate-200 dark:focus:ring-slate-700">
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </aside>
</div>
