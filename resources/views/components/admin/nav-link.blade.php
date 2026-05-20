@props([
    'href' => '#',
    'active' => false,
    'icon' => null,
])

<a href="{{ $href }}"
   {{ $attributes->class([
        'group flex items-center gap-3 rounded-2xl px-4 py-3 text-sm font-semibold transition',
        'border border-indigo-100 bg-indigo-50 text-indigo-700 shadow-sm dark:border-indigo-400/20 dark:bg-indigo-500/15 dark:text-indigo-200' => $active,
        'text-slate-600 hover:bg-slate-100 hover:text-slate-950 dark:text-slate-400 dark:hover:bg-slate-900 dark:hover:text-white' => ! $active,
   ]) }}>
    @if ($icon)
        <span class="flex h-9 w-9 items-center justify-center rounded-xl {{ $active ? 'bg-white text-indigo-700 shadow-sm dark:bg-indigo-500 dark:text-white' : 'bg-slate-100 text-slate-500 group-hover:bg-white group-hover:text-slate-800 dark:bg-slate-900 dark:text-slate-400 dark:group-hover:bg-slate-800 dark:group-hover:text-white' }}">
            {{ $icon }}
        </span>
    @endif
    <span>{{ $slot }}</span>
</a>
