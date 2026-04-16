@props([
    'title' => null,
    'icon' => null,
    'flush' => false,
])

<section {{ $attributes->class(['ethos-section', 'ethos-section--flush' => $flush]) }}>
    @if ($title || isset($actions))
        <header class="ethos-section__head">
            @if ($title)
                <h2 class="ethos-section__title">
                    @if ($icon)
                        <i class="ti {{ $icon }}" aria-hidden="true"></i>
                    @endif
                    <span>{{ $title }}</span>
                </h2>
            @endif

            @isset($actions)
                <div class="d-flex gap-2 align-items-center">
                    {{ $actions }}
                </div>
            @endisset
        </header>
    @endif

    <div class="ethos-section__body">
        {{ $slot }}
    </div>
</section>
