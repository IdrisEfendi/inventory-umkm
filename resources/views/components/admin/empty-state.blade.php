@props([
    'title',
    'description' => null,
    'actionLabel' => null,
    'actionUrl' => null,
])

<div class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 px-5 py-8 text-center">
    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-white text-xl shadow-sm ring-1 ring-slate-200">📦</div>
    <h3 class="mt-4 text-sm font-bold text-slate-900">{{ $title }}</h3>
    @if ($description)
        <p class="mx-auto mt-2 max-w-sm text-sm leading-6 text-slate-500">{{ $description }}</p>
    @endif
    @if ($actionLabel && $actionUrl)
        <a href="{{ $actionUrl }}" class="mt-5 inline-flex items-center justify-center rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-indigo-700 focus:outline-none focus:ring-4 focus:ring-indigo-200">
            {{ $actionLabel }}
        </a>
    @endif
</div>
