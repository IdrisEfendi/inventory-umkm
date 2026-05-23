<!DOCTYPE html>
<html lang="id" class="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') - Inventory UMKM</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class'
        };
    </script>
    <script>
        (function () {
            const savedTheme = localStorage.getItem('inventory-umkm-theme');
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            const shouldUseDark = savedTheme === 'dark' || (!savedTheme && prefersDark);

            document.documentElement.classList.toggle('dark', shouldUseDark);
            document.documentElement.classList.toggle('light', !shouldUseDark);
        })();
    </script>
</head>
<body class="min-h-screen bg-slate-50 text-slate-900 antialiased transition-colors duration-300 dark:bg-slate-950 dark:text-slate-100">
    <x-admin.sidebar />

    <div class="lg:pl-72">
        <x-admin.topbar :title="$title ?? trim($__env->yieldContent('title', 'Dashboard'))" :subtitle="$subtitle ?? null" />

        <main class="px-4 py-6 sm:px-6 lg:px-8 dark:[&_.bg-white]:bg-slate-900 dark:[&_.bg-slate-50]:bg-slate-800 dark:[&_.border-slate-100]:border-slate-800 dark:[&_.border-slate-200]:border-slate-700 dark:[&_.divide-slate-100]:divide-slate-800 dark:[&_.ring-slate-100]:ring-slate-700 dark:[&_.ring-slate-200]:ring-slate-700 dark:[&_.text-slate-950]:text-white dark:[&_.text-slate-900]:text-slate-100 dark:[&_.text-slate-800]:text-slate-100 dark:[&_.text-slate-700]:text-slate-200 dark:[&_.text-slate-600]:text-slate-300 dark:[&_.text-slate-500]:text-slate-400 dark:[&_.text-slate-400]:text-slate-500">
            <div class="mx-auto max-w-7xl">
                @yield('content')
            </div>
        </main>
    </div>

    <script>
        (function () {
            const toggle = document.getElementById('theme-toggle');
            const label = document.getElementById('theme-toggle-label');
            const sunIcon = document.getElementById('theme-icon-sun');
            const moonIcon = document.getElementById('theme-icon-moon');
            const sidebar = document.getElementById('mobile-sidebar');
            const sidebarPanel = document.getElementById('mobile-sidebar-panel');
            const openSidebar = document.getElementById('open-sidebar');
            const closeSidebar = document.querySelectorAll('[data-close-sidebar]');

            function syncThemeButton() {
                const isDark = document.documentElement.classList.contains('dark');

                if (label) {
                    label.textContent = isDark ? 'Light Mode' : 'Dark Mode';
                }

                if (sunIcon && moonIcon) {
                    sunIcon.classList.toggle('hidden', !isDark);
                    moonIcon.classList.toggle('hidden', isDark);
                }
            }

            function setSidebar(open) {
                if (!sidebar || !sidebarPanel) return;

                sidebar.classList.toggle('pointer-events-none', !open);
                sidebar.classList.toggle('opacity-0', !open);
                sidebar.classList.toggle('opacity-100', open);
                sidebarPanel.classList.toggle('-translate-x-full', !open);
                sidebarPanel.classList.toggle('translate-x-0', open);
                document.body.classList.toggle('overflow-hidden', open);
            }

            if (toggle) {
                toggle.addEventListener('click', function () {
                    const isDark = !document.documentElement.classList.contains('dark');
                    document.documentElement.classList.toggle('dark', isDark);
                    document.documentElement.classList.toggle('light', !isDark);
                    localStorage.setItem('inventory-umkm-theme', isDark ? 'dark' : 'light');
                    syncThemeButton();
                });
            }

            if (openSidebar) {
                openSidebar.addEventListener('click', function () {
                    setSidebar(true);
                });
            }

            closeSidebar.forEach(function (button) {
                button.addEventListener('click', function () {
                    setSidebar(false);
                });
            });

            document.addEventListener('keydown', function (event) {
                if (event.key === 'Escape') {
                    setSidebar(false);
                }
            });

            syncThemeButton();
            setSidebar(false);
        })();
    </script>
</body>
</html>
