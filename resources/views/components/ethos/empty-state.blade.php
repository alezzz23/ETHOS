@props([
    'icon' => 'ti-inbox',
    'title' => 'No hay datos',
    'description' => null,
    'inline' => false,
])

<div {{ $attributes->class(['ethos-empty', 'ethos-empty--inline' => $inline])->merge(['role' => 'status']) }}>
    <span class="ethos-empty__icon" aria-hidden="true">
        <i class="ti {{ $icon }}"></i>
    </span>
    <p class="ethos-empty__title">{{ $title }}</p>

    @if ($description)
        <p class="ethos-empty__desc">{{ $description }}</p>
    @endif

    {{-- Slot libre: botones / enlaces de call-to-action --}}
    @if (trim($slot) !== '')
        <div class="d-flex gap-2 flex-wrap justify-content-center mt-1">
            {{ $slot }}
        </div>
    @endif
</div>
