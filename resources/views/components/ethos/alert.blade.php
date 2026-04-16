@props([
    'variant' => 'info', // info | success | warning | danger
    'title' => null,
    'icon' => null,
    'dismissible' => false,
])

@php
    $variants = ['info', 'success', 'warning', 'danger'];
    $variant = in_array($variant, $variants, true) ? $variant : 'info';
    $defaultIcons = [
        'info'    => 'ti-info-circle',
        'success' => 'ti-circle-check',
        'warning' => 'ti-alert-triangle',
        'danger'  => 'ti-alert-octagon',
    ];
    $icon = $icon ?? $defaultIcons[$variant];
@endphp

<div {{ $attributes->class(['ethos-alert', "is-{$variant}"])->merge(['role' => $variant === 'danger' ? 'alert' : 'status']) }}>
    <span class="ethos-alert__icon" aria-hidden="true">
        <i class="ti {{ $icon }}"></i>
    </span>
    <div class="ethos-alert__body">
        @if ($title)
            <p class="ethos-alert__title">{{ $title }}</p>
        @endif
        <div>{{ $slot }}</div>
    </div>
    @if ($dismissible)
        <button type="button"
                class="ethos-alert__close"
                aria-label="Cerrar"
                onclick="this.closest('.ethos-alert').remove()">
            <i class="ti ti-x"></i>
        </button>
    @endif
</div>
