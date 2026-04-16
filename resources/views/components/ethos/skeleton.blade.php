@props([
    'type' => 'line', // line | title | avatar | card
    'width' => null,
    'height' => null,
    'count' => 1,
])

@php
    $valid = ['line', 'title', 'avatar', 'card'];
    $type = in_array($type, $valid, true) ? $type : 'line';
    $style = '';
    if ($width)  { $style .= "width: {$width};"; }
    if ($height) { $style .= "height: {$height};"; }
@endphp

@for ($i = 0; $i < max(1, (int) $count); $i++)
    <span {{ $attributes->class(['ethos-skeleton', "ethos-skeleton--{$type}"])->merge([
        'style' => $style,
        'aria-hidden' => 'true',
    ]) }}></span>
@endfor
