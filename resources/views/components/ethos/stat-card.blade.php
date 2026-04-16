@props([
    'title',
    'value',
    'icon' => null,
    'variant' => 'primary',
    'delta' => null,
    'deltaDirection' => null,
    'sub' => null,
    'href' => null,
])

@php
    $variants = ['primary', 'info', 'success', 'warning', 'danger', 'secondary'];
    $variant = in_array($variant, $variants, true) ? $variant : 'primary';

    $direction = $deltaDirection;
    if ($direction === null && $delta !== null && is_numeric($delta)) {
        $direction = $delta > 0 ? 'up' : ($delta < 0 ? 'down' : 'flat');
    }
    $deltaClass = match ($direction) {
        'down' => 'is-down',
        'flat' => 'is-flat',
        default => '',
    };
    $deltaIcon = match ($direction) {
        'up'   => 'ti-trending-up',
        'down' => 'ti-trending-down',
        'flat' => 'ti-minus',
        default => null,
    };
@endphp

<div {{ $attributes->class(['card ethos-stat-card', "is-{$variant}"]) }}>
    <div class="ethos-stat-body">
        <div class="d-flex align-items-start justify-content-between gap-3">
            <div class="flex-grow-1 min-w-0">
                <p class="ethos-stat-title">{{ $title }}</p>
                <h3 class="ethos-stat-value">{{ $value }}</h3>

                @isset($sub)
                    <div class="ethos-stat-sub">{{ $sub }}</div>
                @endisset

                @if ($delta !== null)
                    <span class="ethos-stat-delta {{ $deltaClass }}">
                        @if ($deltaIcon)
                            <i class="ti {{ $deltaIcon }}" aria-hidden="true"></i>
                        @endif
                        <span>{{ is_numeric($delta) ? (($delta > 0 ? '+' : '') . $delta . '%') : $delta }}</span>
                    </span>
                @endif
            </div>

            @if ($icon)
                <span class="ethos-stat-icon bg-label-{{ $variant }}" aria-hidden="true">
                    <i class="ti {{ $icon }}"></i>
                </span>
            @endif
        </div>

        @if ($href)
            <a href="{{ $href }}" class="stretched-link" aria-label="Ver detalle: {{ $title }}"></a>
        @endif
    </div>
</div>
