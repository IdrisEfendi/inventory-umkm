<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - Inventory UMKM</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-slate-950 text-slate-900 antialiased">
    <main class="relative flex min-h-screen items-center justify-center overflow-hidden px-4 py-8">
        <div class="absolute inset-0 -z-10 bg-[radial-gradient(circle_at_top_left,_rgba(37,99,235,.35),_transparent_34%),radial-gradient(circle_at_bottom_right,_rgba(16,185,129,.28),_transparent_36%),linear-gradient(135deg,_#020617_0%,_#0f172a_45%,_#111827_100%)]"></div>

        <section class="grid w-full max-w-5xl overflow-hidden rounded-[2rem] bg-white shadow-2xl shadow-black/30 md:grid-cols-[1.05fr_.95fr]">
            <aside class="relative hidden min-h-[620px] bg-slate-950 p-10 text-white md:flex md:flex-col md:justify-between">
                <div class="absolute inset-0 bg-[radial-gradient(circle_at_20%_20%,_rgba(59,130,246,.36),_transparent_30%),radial-gradient(circle_at_80%_70%,_rgba(16,185,129,.28),_transparent_32%)]"></div>

                <div class="relative">
                    <div class="inline-flex items-center gap-3 rounded-2xl bg-white/10 px-4 py-3 ring-1 ring-white/15">
                        <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-white text-sm font-black text-emerald-700 shadow-lg">INV</div>
                        <div>
                            <p class="text-sm font-semibold text-white">Inventory UMKM</p>
                            <p class="text-xs text-emerald-100">Sales & Stock Dashboard</p>
                        </div>
                    </div>

                    <div class="mt-12 max-w-md">
                        <p class="mb-4 inline-flex rounded-full bg-emerald-500/15 px-3 py-1 text-xs font-semibold uppercase tracking-[0.22em] text-emerald-100 ring-1 ring-emerald-300/20">Admin Panel</p>
                        <h1 class="text-4xl font-bold leading-tight tracking-tight">Kelola stok, penjualan, dan laporan UMKM dalam satu dashboard.</h1>
                        <p class="mt-5 text-sm leading-6 text-slate-300">Masuk untuk mengelola produk, supplier, transaksi stok, penjualan, invoice, dan laporan bisnis.</p>
                    </div>
                </div>

                <div class="relative grid grid-cols-3 gap-3">
                    <div class="rounded-2xl bg-white/10 p-4 ring-1 ring-white/10">
                        <p class="text-2xl font-bold">01</p>
                        <p class="mt-1 text-xs text-slate-300">Produk</p>
                    </div>
                    <div class="rounded-2xl bg-white/10 p-4 ring-1 ring-white/10">
                        <p class="text-2xl font-bold">02</p>
                        <p class="mt-1 text-xs text-slate-300">Stok</p>
                    </div>
                    <div class="rounded-2xl bg-white/10 p-4 ring-1 ring-white/10">
                        <p class="text-2xl font-bold">03</p>
                        <p class="mt-1 text-xs text-slate-300">Sales</p>
                    </div>
                </div>
            </aside>

            <section class="px-6 py-8 sm:px-10 sm:py-12">
                <div class="mx-auto w-full max-w-md">
                    <div class="mb-8 md:hidden">
                        <div class="mb-5 flex h-14 w-14 items-center justify-center rounded-2xl bg-emerald-600 text-sm font-black text-white shadow-lg shadow-emerald-600/25">INV</div>
                        <p class="text-sm font-semibold uppercase tracking-[0.18em] text-emerald-600">Inventory UMKM</p>
                    </div>

                    <div>
                        <h2 class="text-2xl font-bold tracking-tight text-slate-950 sm:text-3xl">Masuk ke Admin</h2>
                        <p class="mt-2 text-sm leading-6 text-slate-600">Gunakan akun admin untuk melanjutkan ke dashboard inventory.</p>
                    </div>

                    @if (session('status'))
                        <div role="status" class="mt-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700">
                            {{ session('status') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div role="alert" aria-live="polite" class="mt-6 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                            <p class="font-semibold">Login gagal</p>
                            <p class="mt-0.5">{{ $errors->first() }}</p>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login.store') }}" class="mt-7 space-y-5" id="loginForm">
                        @csrf

                        <div>
                            <label for="email" class="mb-2 block text-sm font-semibold text-slate-700">Email</label>
                            <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" required autofocus class="block w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-slate-900 shadow-sm outline-none transition placeholder:text-slate-400 hover:border-slate-400 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100" placeholder="admin@example.com">
                        </div>

                        <div>
                            <div class="mb-2 flex items-center justify-between gap-3">
                                <label for="password" class="block text-sm font-semibold text-slate-700">Password</label>
                                <span class="text-xs text-slate-500">Wajib diisi</span>
                            </div>
                            <input id="password" name="password" type="password" autocomplete="current-password" required class="block w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-slate-900 shadow-sm outline-none transition placeholder:text-slate-400 hover:border-slate-400 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100" placeholder="Masukkan password">
                        </div>

                        <label class="flex items-center gap-3 text-sm text-slate-600">
                            <input type="checkbox" name="remember" value="1" class="h-4 w-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                            Ingat saya
                        </label>

                        <button type="submit" id="loginButton" class="flex w-full items-center justify-center gap-2 rounded-2xl bg-emerald-600 px-5 py-3 font-semibold text-white shadow-lg shadow-emerald-600/25 transition hover:bg-emerald-700 focus:outline-none focus:ring-4 focus:ring-emerald-200 active:bg-emerald-800 disabled:cursor-not-allowed disabled:opacity-70">
                            <svg id="loginSpinner" class="hidden h-5 w-5 animate-spin" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 0 1 8-8v4a4 4 0 0 0-4 4H4Z"></path>
                            </svg>
                            <span id="loginButtonText">Masuk</span>
                        </button>
                    </form>
                </div>
            </section>
        </section>
    </main>

    <script>
        (function () {
            var form = document.getElementById('loginForm');
            var button = document.getElementById('loginButton');
            var text = document.getElementById('loginButtonText');
            var spinner = document.getElementById('loginSpinner');

            if (!form || !button || !text || !spinner) return;

            form.addEventListener('submit', function () {
                button.disabled = true;
                text.textContent = 'Memproses...';
                spinner.classList.remove('hidden');
            });
        })();
    </script>
</body>
</html>
