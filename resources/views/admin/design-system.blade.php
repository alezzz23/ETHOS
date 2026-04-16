@extends('layouts.vuexy')
@section('title', 'Guía de componentes ETHOS')

@section('content')
<x-ethos.page-header
    eyebrow="Design system"
    title="Componentes ETHOS"
    description="Vitrina de componentes Blade reutilizables. Copia los snippets y usalos en cualquier vista de admin."
    :breadcrumbs="[
        ['label' => 'Inicio',     'url' => route('admin.dashboard')],
        ['label' => 'Componentes'],
    ]"
>
    <a href="#stat-card" class="btn btn-sm btn-label-primary">
        <i class="ti ti-hash" aria-hidden="true"></i> Índice
    </a>
</x-ethos.page-header>

{{-- ── Tipografía ──────────────────────────────────────────────── --}}
<x-ethos.section id="typography" title="Tipografía y jerarquía" icon="ti-typography" class="mb-4">
    <x-slot:actions>
        <code class="text-muted small">Fraunces · Inter Tight · JetBrains Mono</code>
    </x-slot:actions>

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="ethos-eyebrow">Display · Fraunces</div>
            <div class="ethos-display mb-2">Consultoría con evidencia.</div>
            <p class="ethos-lead">
                Lead · Inter Tight 400. Las descripciones largas usan <code>.ethos-lead</code> para
                ganar respiración visual y un ancho máximo de 68ch que favorece la lectura.
            </p>

            <hr class="my-4">

            <h1 class="mb-1">H1 · Título de página</h1>
            <h2 class="mb-1">H2 · Sección</h2>
            <h3 class="mb-1">H3 · Subsección</h3>
            <h4 class="mb-1">H4 · Bloque</h4>
            <h5 class="mb-1">H5 · Card</h5>
            <h6 class="mb-3">H6 · Etiqueta en caps</h6>

            <p>
                Texto corriente en <strong>Inter Tight</strong>. Esta familia ofrece formas compactas
                y un ritmo vertical equilibrado para densidades de dashboard. Los énfasis
                usan <strong>peso 600</strong> en lugar de 700 para evitar saturación.
            </p>
            <p class="ethos-caption">
                Caption · se usa para metadata, fechas y avisos legales.
            </p>
        </div>

        <div class="col-lg-5">
            <div class="card border-0" style="background: color-mix(in srgb, var(--vz-primary) 6%, transparent);">
                <div class="card-body">
                    <div class="ethos-eyebrow mb-2">Numérico · JetBrains Mono</div>
                    <div class="d-flex align-items-baseline gap-2 mb-3">
                        <span class="ethos-numeric" style="font-size: 2.5rem;">$ 1.284.500</span>
                        <span class="text-muted small">ARS</span>
                    </div>
                    <table class="table table-sm mb-0">
                        <thead>
                            <tr>
                                <th>Concepto</th>
                                <th class="text-end">Horas</th>
                                <th class="text-end">Monto</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Análisis</td>
                                <td class="text-end ethos-cell-num">12.5</td>
                                <td class="text-end ethos-cell-num">$ 187.500</td>
                            </tr>
                            <tr>
                                <td>Implementación</td>
                                <td class="text-end ethos-cell-num">48.0</td>
                                <td class="text-end ethos-cell-num">$ 720.000</td>
                            </tr>
                            <tr>
                                <td>Seguimiento</td>
                                <td class="text-end ethos-cell-num">24.5</td>
                                <td class="text-end ethos-cell-num">$ 367.500</td>
                            </tr>
                            <tr class="fw-semibold">
                                <td>Total</td>
                                <td class="text-end ethos-cell-num">85.0</td>
                                <td class="text-end ethos-cell-num">$ 1.275.000</td>
                            </tr>
                        </tbody>
                    </table>
                    <p class="ethos-caption mt-3 mb-0">
                        Los numéricos usan <code>font-variant-numeric: tabular-nums</code> para que
                        las columnas se alineen perfectamente.
                    </p>
                </div>
            </div>

            <div class="mt-3 p-3 rounded border bg-light">
                <div class="ethos-brand" style="font-size: 1.25rem;">ETHOS</div>
                <div class="ethos-caption">Brand mark · Cinzel (solo logo / wordmark)</div>
            </div>
        </div>
    </div>
</x-ethos.section>

{{-- ── Stat card ────────────────────────────────────────────────── --}}
<x-ethos.section id="stat-card" title="Stat card" icon="ti-chart-histogram" class="mb-4">
    <x-slot:actions>
        <code class="text-muted small">&lt;x-ethos.stat-card /&gt;</code>
    </x-slot:actions>

    <div class="row g-3">
        <div class="col-xl-3 col-md-6">
            <x-ethos.stat-card
                title="Clientes"
                value="142"
                icon="ti-users"
                variant="primary"
                :delta="8"
                sub="vs. mes anterior"
            />
        </div>
        <div class="col-xl-3 col-md-6">
            <x-ethos.stat-card
                title="Proyectos activos"
                value="37"
                icon="ti-briefcase"
                variant="info"
                :delta="12"
            />
        </div>
        <div class="col-xl-3 col-md-6">
            <x-ethos.stat-card
                title="Propuestas pendientes"
                value="6"
                icon="ti-file-description"
                variant="warning"
                :delta="-3"
                sub="últimos 7 días"
            />
        </div>
        <div class="col-xl-3 col-md-6">
            <x-ethos.stat-card
                title="Tasa de cierre"
                value="72%"
                icon="ti-target-arrow"
                variant="success"
                delta-direction="flat"
                delta="estable"
            />
        </div>
    </div>
</x-ethos.section>

{{-- ── Empty state ──────────────────────────────────────────────── --}}
<x-ethos.section id="empty-state" title="Empty state" icon="ti-inbox" class="mb-4">
    <x-slot:actions>
        <code class="text-muted small">&lt;x-ethos.empty-state /&gt;</code>
    </x-slot:actions>

    <div class="row g-3">
        <div class="col-md-6">
            <div class="border rounded p-2" style="border-style: dashed !important;">
                <x-ethos.empty-state
                    icon="ti-file-off"
                    title="No hay propuestas todavía"
                    description="Creá una propuesta desde la pestaña Fase 2 una vez el proyecto esté en análisis."
                >
                    <a href="#" class="btn btn-sm btn-primary">
                        <i class="ti ti-plus" aria-hidden="true"></i> Nueva propuesta
                    </a>
                </x-ethos.empty-state>
            </div>
        </div>
        <div class="col-md-6">
            <div class="border rounded p-2" style="border-style: dashed !important;">
                <x-ethos.empty-state
                    icon="ti-search-off"
                    title="Sin resultados"
                    description="Probá con otra búsqueda o limpiá los filtros aplicados."
                    inline
                />
            </div>
        </div>
    </div>
</x-ethos.section>

{{-- ── Skeleton ─────────────────────────────────────────────────── --}}
<x-ethos.section id="skeleton" title="Skeleton (loading)" icon="ti-loader" class="mb-4">
    <x-slot:actions>
        <code class="text-muted small">&lt;x-ethos.skeleton /&gt;</code>
    </x-slot:actions>

    <div class="row g-3">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <x-ethos.skeleton type="title" />
                    <x-ethos.skeleton type="line" width="90%" />
                    <div class="mt-2"></div>
                    <x-ethos.skeleton type="line" width="70%" />
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body d-flex align-items-center gap-3">
                    <x-ethos.skeleton type="avatar" />
                    <div class="flex-grow-1">
                        <x-ethos.skeleton type="line" width="60%" />
                        <div class="mt-2"></div>
                        <x-ethos.skeleton type="line" width="80%" />
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <x-ethos.skeleton type="card" />
        </div>
    </div>
</x-ethos.section>

{{-- ── Alerts ──────────────────────────────────────────────────── --}}
<x-ethos.section id="alert" title="Alertas inline" icon="ti-bell" class="mb-4">
    <x-slot:actions>
        <code class="text-muted small">&lt;x-ethos.alert /&gt;</code>
    </x-slot:actions>

    <div class="d-flex flex-column gap-2">
        <x-ethos.alert variant="info" title="Información">
            Las propuestas se regeneran automáticamente al modificar horas o tarifa.
        </x-ethos.alert>
        <x-ethos.alert variant="success" title="Guardado">
            Los cambios se aplicaron correctamente.
        </x-ethos.alert>
        <x-ethos.alert variant="warning" dismissible>
            Tu sesión expirará en 5 minutos.
        </x-ethos.alert>
        <x-ethos.alert variant="danger" title="Error de validación" dismissible>
            El correo ya está registrado por otro usuario.
        </x-ethos.alert>
    </div>
</x-ethos.section>

{{-- ── Status badge ─────────────────────────────────────────────── --}}
<x-ethos.section id="badge" title="Status badge" icon="ti-flag" class="mb-4">
    <x-slot:actions>
        <code class="text-muted small">&lt;x-ethos.status-badge /&gt;</code>
    </x-slot:actions>

    <div class="d-flex gap-2 flex-wrap">
        <x-ethos.status-badge status="capturado" />
        <x-ethos.status-badge status="en_analisis" />
        <x-ethos.status-badge status="aprobado" />
        <x-ethos.status-badge status="en_ejecucion" />
        <x-ethos.status-badge status="cerrado" />
        <span class="vr mx-2"></span>
        <x-ethos.status-badge status="draft" />
        <x-ethos.status-badge status="sent" />
        <x-ethos.status-badge status="approved" />
        <x-ethos.status-badge status="rejected" />
    </div>
</x-ethos.section>
@endsection
