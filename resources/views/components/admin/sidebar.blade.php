@php
    $links = [
        ['label' => 'Dashboard', 'route' => 'dashboard', 'icon' => '⌂'],
        ['label' => 'Produk', 'route' => 'products.index', 'icon' => '📦'],
        ['label' => 'Kategori', 'route' => 'categories.index', 'icon' => '🏷️'],
        ['label' => 'Stok', 'route' => 'stock-movements.index', 'icon' => '⇄'],
        ['label' => 'Penjualan', 'route' => 'sales.index', 'icon' => '🧾'],
        ['label' => 'Laporan', 'route' => null, 'icon' => '📊'],
    ];
@endphp

<aside class="hidden w-72 shrink-0 border-r border-slate-200 bg-white transition-colors duration-300 dark:border-slate-800 dark:bg-slate-950 lg:fixed lg:inset-y-0 lg:flex lg:flex-col">
    <div class="flex h-20 items-center gap-3 border-b border-slate-200 px-6 dark:border-slate-800">
        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gradient-to-br from-indigo-600 to-sky-500 text-sm font-black text-white shadow-lg shadow-indigo-600/25">INV</div>
        <div>
            <p class="text-sm font-black text-slate-950 dark:text-white">Inventory UMKM</p>
            <p class="text-xs font-medium text-slate-500 dark:text-slate-400">Sales & Stock</p>
        </div>
    </div>

    <nav class="flex-1 space-y-1 overflow-y-auto px-4 py-6">
        @foreach ($links as $link)
            @php
                $href = $link['route'] ? route($link['route']) : '#';
                $active = $link['route'] ? request()->routeIs($link['route']) : false;
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
