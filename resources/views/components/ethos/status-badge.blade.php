@props([
    'status',
    'map' => [],
    'labels' => [],
])

@php
    // Mapa por defecto para estados habituales del proyecto ETHOS.
    $defaultMap = [
        // Projects
        'capturado'     => 'bg-label-primary',
        'en_analisis'   => 'bg-label-warning',
        'aprobado'      => 'bg-label-info',
        'en_ejecucion'  => 'bg-label-dark',
        'cerrado'       => 'bg-label-success',
        // Proposals
        'draft'     => 'bg-label-secondary',
        'sent'      => 'bg-label-info',
        'approved'  => 'bg-label-success',
        'rejected'  => 'bg-label-danger',
        'expired'   => 'bg-label-warning',
        // Tasks
        'pending'     => 'bg-label-secondary',
        'in_progress' => 'bg-label-info',
        'completed'   => 'bg-label-success',
        'escalated'   => 'bg-label-danger',
        // Generic
        'active'    => 'bg-label-success',
        'inactive'  => 'bg-label-secondary',
    ];

    $defaultLabels = [
        'capturado'     => 'Capturado',
        'en_analisis'   => 'En análisis',
        'aprobado'      => 'Aprobado',
        'en_ejecucion'  => 'En ejecución',
        'cerrado'       => 'Cerrado',
        'draft'         => 'Borrador',
        'sent'          => 'Enviada',
        'approved'      => 'Aprobada',
        'rejected'      => 'Rechazada',
        'expired'       => 'Expirada',
        'pending'       => 'Pendiente',
        'in_progress'   => 'En progreso',
        'completed'     => 'Completada',
        'escalated'     => 'Escalada',
        'active'        => 'Activo',
        'inactive'      => 'Inactivo',
    ];

    $classMap  = array_merge($defaultMap, $map);
    $labelsMap = array_merge($defaultLabels, $labels);

    $class = $classMap[$status] ?? 'bg-label-secondary';
    $label = $labelsMap[$status] ?? ucfirst(str_replace('_', ' ', (string) $status));
@endphp

<span {{ $attributes->class(['badge', $class]) }}>{{ $label }}</span>
