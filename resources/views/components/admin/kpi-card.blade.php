@props([
    'label',
    'value',
    'description' => null,
    'icon' => '•',
    'tone' => 'indigo',
])

@php
    $tones = [
        'indigo' => 'bg-indigo-50 text-indigo-600 ring-indigo-100 dark:bg-indigo-500/15 dark:text-indigo-200 dark:ring-indigo-400/20',
        'emerald' => 'bg-emerald-50 text-emerald-600 ring-emerald-100 dark:bg-emerald-500/15 dark:text-emerald-200 dark:ring-emerald-400/20',
        'amber' => 'bg-amber-50 text-amber-600 ring-amber-100 dark:bg-amber-500/15 dark:text-amber-200 dark:ring-amber-400/20',
        'rose' => 'bg-rose-50 text-rose-600 ring-rose-100 dark:bg-rose-500/15 dark:text-rose-200 dark:ring-rose-400/20',
        'sky' => 'bg-sky-50 text-sky-600 ring-sky-100 dark:bg-sky-500/15 dark:text-sky-200 dark:ring-sky-400/20',
    ];
    $toneClass = $tones[$tone] ?? $tones['indigo'];
@endphp

<div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md dark:border-slate-700 dark:bg-slate-900">
    <div class="flex items-start justify-between gap-4">
        <div class="min-w-0">
            <p class="text-sm font-medium text-slate-500 dark:text-slate-400">{{ $label }}</p>
            <p class="mt-3 truncate text-2xl font-bold tracking-tight text-slate-950 dark:text-white">{{ $value }}</p>
        </div>
        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl text-lg font-bold ring-1 {{ $toneClass }}">
            {{ $icon }}
        </div>
    </div>

    @if ($description)
        <p class="mt-4 text-xs leading-5 text-slate-500 dark:text-slate-400">{{ $description }}</p>
    @endif
</div>
