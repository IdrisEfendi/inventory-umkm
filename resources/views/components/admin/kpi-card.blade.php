@props([
    'label',
    'value',
    'description' => null,
    'icon' => '•',
    'tone' => 'indigo',
])

@php
    $tones = [
        'indigo' => 'bg-indigo-50 text-indigo-600',
        'emerald' => 'bg-emerald-50 text-emerald-600',
        'amber' => 'bg-amber-50 text-amber-600',
        'rose' => 'bg-rose-50 text-rose-600',
        'sky' => 'bg-sky-50 text-sky-600',
    ];
    $toneClass = $tones[$tone] ?? $tones['indigo'];
@endphp

<div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
    <div class="flex items-start justify-between gap-4">
        <div>
            <p class="text-sm font-medium text-slate-500">{{ $label }}</p>
            <p class="mt-3 text-2xl font-bold tracking-tight text-slate-950">{{ $value }}</p>
        </div>
        <div class="flex h-12 w-12 items-center justify-center rounded-2xl text-lg font-bold {{ $toneClass }}">
            {{ $icon }}
        </div>
    </div>

    @if ($description)
        <p class="mt-4 text-xs leading-5 text-slate-500">{{ $description }}</p>
    @endif
</div>
