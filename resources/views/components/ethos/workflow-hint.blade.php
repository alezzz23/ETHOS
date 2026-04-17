@props([
    'eyebrow' => 'Que sigue',
    'title',
    'message' => null,
    'icon' => 'ti-route-alt-left',
    'steps' => [],
    'ctaLabel' => null,
    'ctaHref' => null,
    'ctaTarget' => null,
    'secondaryLabel' => null,
    'secondaryHref' => null,
    'secondaryTarget' => null,
    'storageKey' => null,
    'dismissible' => true,
])

@php
    $hasSteps = is_array($steps) && count($steps) > 0;
    $hasPrimary = filled($ctaLabel) && filled($ctaHref);
    $hasSecondary = filled($secondaryLabel) && filled($secondaryHref);
@endphp

<aside {{ $attributes->class('ethos-workflow-card')->merge($storageKey ? ['data-workflow-card' => $storageKey] : []) }}>
    <div class="ethos-workflow-card__glow" aria-hidden="true"></div>
    <div class="ethos-workflow-card__icon" aria-hidden="true">
        <i class="ti {{ $icon }}"></i>
    </div>

    <div class="ethos-workflow-card__body">
        <p class="ethos-workflow-card__eyebrow">{{ $eyebrow }}</p>
        <h5 class="ethos-workflow-card__title">{{ $title }}</h5>

        @if (filled($message))
            <p class="ethos-workflow-card__message">{{ $message }}</p>
        @endif

        @if ($hasSteps)
            <ol class="ethos-workflow-card__steps">
                @foreach ($steps as $step)
                    <li>{{ $step }}</li>
                @endforeach
            </ol>
        @endif

        @if ($slot->isNotEmpty())
            <div class="ethos-workflow-card__note">
                {{ $slot }}
            </div>
        @endif

        @if ($hasPrimary || $hasSecondary)
            <div class="ethos-workflow-card__actions">
                @if ($hasPrimary)
                    <a href="{{ $ctaHref }}" class="btn btn-primary btn-sm" @if($ctaTarget) target="{{ $ctaTarget }}" @endif>
                        {{ $ctaLabel }}
                    </a>
                @endif

                @if ($hasSecondary)
                    <a href="{{ $secondaryHref }}" class="btn btn-label-secondary btn-sm" @if($secondaryTarget) target="{{ $secondaryTarget }}" @endif>
                        {{ $secondaryLabel }}
                    </a>
                @endif
            </div>
        @endif
    </div>

    @if ($dismissible)
        <button type="button" class="ethos-workflow-card__dismiss" data-workflow-dismiss aria-label="Ocultar ayuda visual">
            <i class="ti ti-x"></i>
        </button>
    @endif
</aside>