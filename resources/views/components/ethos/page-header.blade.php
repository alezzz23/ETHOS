@props([
    'title',
    'eyebrow' => null,
    'description' => null,
    'breadcrumbs' => [], // [['label' => 'Inicio', 'url' => route('...'), 'current' => false], ...]
])

<header {{ $attributes->class(['ethos-page-header']) }}>
    <div class="ethos-page-header__main">
        @if (! empty($breadcrumbs))
            <ol class="ethos-breadcrumb" aria-label="Ruta de navegación">
                @foreach ($breadcrumbs as $crumb)
                    @php
                        $label   = $crumb['label']   ?? '';
                        $url     = $crumb['url']     ?? null;
                        $current = ! empty($crumb['current']) || $loop->last;
                    @endphp
                    <li @if ($current) aria-current="page" @endif>
                        @if ($url && ! $current)
                            <a href="{{ $url }}">{{ $label }}</a>
                        @else
                            <span>{{ $label }}</span>
                        @endif
                    </li>
                @endforeach
            </ol>
        @endif

        @if ($eyebrow)
            <div class="ethos-page-header__eyebrow">{{ $eyebrow }}</div>
        @endif

        <h1 class="ethos-page-header__title">{{ $title }}</h1>

        @if ($description)
            <p class="ethos-page-header__desc">{{ $description }}</p>
        @endif
    </div>

    @if (trim($slot) !== '')
        <div class="ethos-page-header__actions">
            {{ $slot }}
        </div>
    @endif
</header>
