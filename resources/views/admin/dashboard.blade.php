@extends('layouts.vuexy')
@section('title', 'Analytics Dashboard')

@section('content')
<!-- Welcome Banner -->
<div class="row mb-4">
    <div class="col-12 animate-fadein">
        <div class="card" style="background: linear-gradient(72.47deg, rgba(115,103,240,0.7) 22.16%, var(--vz-primary) 76.47%); color: #fff; overflow: hidden; position: relative;">
            <div class="card-body" style="position: relative; z-index: 1;">
                <div class="row align-items-center">
                    <div class="col-sm-7">
                        <h4 class="text-white mb-1">¡Bienvenido de vuelta, Admin! 🎉</h4>
                        <p class="mb-2" style="opacity:.85;">Tu rendimiento ha incrementado un 72% este mes. Revisa tu nuevo informe.</p>
                        <a href="#" class="btn btn-sm" style="background: #fff; color: var(--vz-primary); font-weight: 600; border-radius: 6px; padding: .5rem 1.25rem;">Ver Informe</a>
                    </div>
                    <div class="col-sm-5 text-center d-none d-sm-block" style="opacity:.15;font-size:8rem;line-height:1;"><i class="ti ti-trophy"></i></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Stat Cards -->
<div class="row g-4 mb-4">
    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 animate-fadein">
        <div class="card h-100"><div class="card-body">
            <div class="stat-card">
                <div><div class="stat-label">Clientes</div><div class="d-flex align-items-center gap-2"><div class="stat-value">92,647</div><span class="stat-change up"><i class="ti ti-chevron-up" style="font-size:14px;"></i>18.2%</span></div></div>
                <div class="ms-auto"><div id="miniChart1" style="min-height:50px;"></div></div>
            </div>
        </div></div>
    </div>
    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 animate-fadein">
        <div class="card h-100"><div class="card-body">
            <div class="stat-card">
                <div><div class="stat-label">Ingresos</div><div class="d-flex align-items-center gap-2"><div class="stat-value">$142,853</div><span class="stat-change up"><i class="ti ti-chevron-up" style="font-size:14px;"></i>24.6%</span></div></div>
                <div class="ms-auto"><div id="miniChart2" style="min-height:50px;"></div></div>
            </div>
        </div></div>
    </div>
    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 animate-fadein">
        <div class="card h-100"><div class="card-body">
            <div class="stat-card">
                <div><div class="stat-label">Consultorías</div><div class="d-flex align-items-center gap-2"><div class="stat-value">4,679</div><span class="stat-change down"><i class="ti ti-chevron-down" style="font-size:14px;"></i>8.1%</span></div></div>
                <div class="ms-auto"><div id="miniChart3" style="min-height:50px;"></div></div>
            </div>
        </div></div>
    </div>
    <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 animate-fadein">
        <div class="card h-100"><div class="card-body">
            <div class="stat-card">
                <div><div class="stat-label">Proyectos Activos</div><div class="d-flex align-items-center gap-2"><div class="stat-value">256</div><span class="stat-change up"><i class="ti ti-chevron-up" style="font-size:14px;"></i>42.9%</span></div></div>
                <div class="ms-auto"><div id="miniChart4" style="min-height:50px;"></div></div>
            </div>
        </div></div>
    </div>
</div>

<!-- Revenue + Order Stats -->
<div class="row g-4 mb-4">
    <div class="col-xl-8 col-lg-7 animate-fadein">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div><h5 class="card-title">Reporte de Ingresos</h5><p class="card-subtitle mb-0">Comparación interanual</p></div>
                <div class="dropdown">
                    <button class="btn btn-sm dropdown-toggle" data-bs-toggle="dropdown" style="font-size:.8125rem;border:1px solid var(--vz-border-color);color:var(--vz-body-color);border-radius:6px;">2024</button>
                    <ul class="dropdown-menu dropdown-menu-end"><li><a class="dropdown-item" href="#">2024</a></li><li><a class="dropdown-item" href="#">2023</a></li><li><a class="dropdown-item" href="#">2022</a></li></ul>
                </div>
            </div>
            <div class="card-body pt-2">
                <div class="row align-items-end">
                    <div class="col-md-8"><div id="revenueChart" style="min-height:280px;"></div></div>
                    <div class="col-md-4">
                        <div class="text-center px-3 pb-3">
                            <div class="d-flex align-items-center justify-content-center gap-2 mb-2"><div style="width:10px;height:10px;border-radius:50%;background:var(--vz-primary);"></div><span style="font-size:.8125rem;color:var(--vz-body-color);">Ingreso</span></div>
                            <h4 class="mb-0" style="color:var(--vz-heading-color);font-weight:700;">$25,825</h4>
                            <p class="mb-3" style="font-size:.75rem;color:var(--vz-body-color);">Budget: 56,800</p>
                            <div id="weeklyEarningChart" style="min-height:100px;"></div>
                            <span class="badge-label bg-label-success mt-2 d-inline-block">+42.9%</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-4 col-lg-5 animate-fadein">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div><h5 class="card-title">Estadísticas de Servicios</h5><p class="card-subtitle mb-0">42.82k Total de servicios</p></div>
                <div class="dropdown"><button class="btn btn-sm p-1" data-bs-toggle="dropdown" style="background:none;border:none;color:var(--vz-body-color);"><i class="ti ti-dots-vertical" style="font-size:1.25rem;"></i></button><ul class="dropdown-menu dropdown-menu-end"><li><a class="dropdown-item" href="#">Ver Más</a></li><li><a class="dropdown-item" href="#">Exportar</a></li></ul></div>
            </div>
            <div class="card-body pt-1">
                <div class="text-center mb-3"><div id="orderStatsDonut" style="min-height:150px;"></div></div>
                <div class="order-stat-item"><div class="order-stat-icon" style="background:var(--vz-primary-lighter);color:var(--vz-primary);"><i class="ti ti-briefcase"></i></div><div class="order-stat-info"><div class="order-stat-title">Auditoría</div><div class="order-stat-sub">Interna y fiscal</div></div><div class="order-stat-value">82.5k</div></div>
                <div class="order-stat-item"><div class="order-stat-icon" style="background:var(--vz-success-lighter);color:var(--vz-success);"><i class="ti ti-chart-bar"></i></div><div class="order-stat-info"><div class="order-stat-title">Consultoría</div><div class="order-stat-sub">Estratégica</div></div><div class="order-stat-value">23.8k</div></div>
                <div class="order-stat-item"><div class="order-stat-icon" style="background:var(--vz-warning-lighter);color:var(--vz-warning);"><i class="ti ti-file-text"></i></div><div class="order-stat-info"><div class="order-stat-title">Procesos</div><div class="order-stat-sub">Diseño corporativo</div></div><div class="order-stat-value">23.4k</div></div>
                <div class="order-stat-item"><div class="order-stat-icon" style="background:var(--vz-info-lighter);color:var(--vz-info);"><i class="ti ti-building"></i></div><div class="order-stat-info"><div class="order-stat-title">Estructuración</div><div class="order-stat-sub">Organizacional</div></div><div class="order-stat-value">12.5k</div></div>
            </div>
        </div>
    </div>
</div>

<!-- Earning + Support + Sales -->
<div class="row g-4 mb-4">
    <div class="col-xl-4 col-lg-6 animate-fadein">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div><h5 class="card-title">Reporte de Ganancias</h5><p class="card-subtitle mb-0">Resumen semanal</p></div>
                <div class="dropdown"><button class="btn btn-sm p-1" data-bs-toggle="dropdown" style="background:none;border:none;color:var(--vz-body-color);"><i class="ti ti-dots-vertical" style="font-size:1.25rem;"></i></button><ul class="dropdown-menu dropdown-menu-end"><li><a class="dropdown-item" href="#">Ver Más</a></li><li><a class="dropdown-item" href="#">Exportar</a></li></ul></div>
            </div>
            <div class="card-body pt-2">
                <div class="d-flex align-items-center gap-3 mb-3"><div><h3 class="mb-0" style="color:var(--vz-heading-color);font-weight:700;">$468</h3><span class="badge-label bg-label-success">+4.2%</span></div></div>
                <div id="earningBarChart" style="min-height:200px;"></div>
                <div class="mt-3">
                    <div class="d-flex align-items-center justify-content-between mb-2"><div class="d-flex align-items-center gap-2"><div style="width:8px;height:8px;border-radius:50%;background:var(--vz-primary);"></div><span style="font-size:.8125rem;color:var(--vz-heading-color);">Auditoría</span></div><span style="font-size:.8125rem;color:var(--vz-body-color);">$845.17</span></div>
                    <div class="d-flex align-items-center justify-content-between mb-2"><div class="d-flex align-items-center gap-2"><div style="width:8px;height:8px;border-radius:50%;background:var(--vz-info);"></div><span style="font-size:.8125rem;color:var(--vz-heading-color);">Consultoría</span></div><span style="font-size:.8125rem;color:var(--vz-body-color);">$82.87</span></div>
                    <div class="d-flex align-items-center justify-content-between"><div class="d-flex align-items-center gap-2"><div style="width:8px;height:8px;border-radius:50%;background:var(--vz-success);"></div><span style="font-size:.8125rem;color:var(--vz-heading-color);">Procesos</span></div><span style="font-size:.8125rem;color:var(--vz-body-color);">$282.12</span></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-4 col-lg-6 animate-fadein">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div><h5 class="card-title">Seguimiento de Soporte</h5><p class="card-subtitle mb-0">Tickets últimos 7 días</p></div>
                <div class="dropdown"><button class="btn btn-sm p-1" data-bs-toggle="dropdown" style="background:none;border:none;color:var(--vz-body-color);"><i class="ti ti-dots-vertical" style="font-size:1.25rem;"></i></button><ul class="dropdown-menu dropdown-menu-end"><li><a class="dropdown-item" href="#">Ver Más</a></li><li><a class="dropdown-item" href="#">Asignar</a></li></ul></div>
            </div>
            <div class="card-body pt-2">
                <div class="row align-items-center"><div class="col-5"><h3 class="mb-0" style="color:var(--vz-heading-color);font-weight:700;">164</h3><p style="font-size:.8125rem;color:var(--vz-body-color);">Total Tickets</p></div><div class="col-7"><div id="supportTrackerChart" style="min-height:150px;"></div></div></div>
                <div class="row g-3 mt-3">
                    <div class="col-4"><div class="d-flex align-items-center gap-2 mb-1"><div style="width:6px;height:6px;border-radius:50%;background:var(--vz-primary);"></div><span style="font-size:.75rem;color:var(--vz-body-color);">Nuevos</span></div><h6 class="mb-0" style="color:var(--vz-heading-color);font-weight:600;">29</h6></div>
                    <div class="col-4"><div class="d-flex align-items-center gap-2 mb-1"><div style="width:6px;height:6px;border-radius:50%;background:var(--vz-success);"></div><span style="font-size:.75rem;color:var(--vz-body-color);">Abiertos</span></div><h6 class="mb-0" style="color:var(--vz-heading-color);font-weight:600;">63</h6></div>
                    <div class="col-4"><div class="d-flex align-items-center gap-2 mb-1"><div style="width:6px;height:6px;border-radius:50%;background:var(--vz-warning);"></div><span style="font-size:.75rem;color:var(--vz-body-color);">Cerrados</span></div><h6 class="mb-0" style="color:var(--vz-heading-color);font-weight:600;">72</h6></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-4 col-lg-12 animate-fadein">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div><h5 class="card-title">Ventas por País</h5><p class="card-subtitle mb-0">Distribución mensual</p></div>
                <div class="dropdown"><button class="btn btn-sm p-1" data-bs-toggle="dropdown" style="background:none;border:none;color:var(--vz-body-color);"><i class="ti ti-dots-vertical" style="font-size:1.25rem;"></i></button><ul class="dropdown-menu dropdown-menu-end"><li><a class="dropdown-item" href="#">Ver Más</a></li></ul></div>
            </div>
            <div class="card-body pt-1">
                <div class="transaction-item mb-3"><div class="transaction-icon" style="background:var(--vz-primary-lighter);color:var(--vz-primary);font-size:1.25rem;">🇻🇪</div><div class="transaction-info"><div class="transaction-title">Venezuela</div><div class="transaction-sub">Caracas, Valencia, Maracaibo</div></div><div class="text-end"><div class="transaction-amount text-success">$9,820</div><div style="font-size:.75rem;color:var(--vz-body-color);">25.8%</div></div></div>
                <div class="transaction-item mb-3"><div class="transaction-icon" style="background:var(--vz-success-lighter);font-size:1.25rem;">🇺🇸</div><div class="transaction-info"><div class="transaction-title">Estados Unidos</div><div class="transaction-sub">Miami, New York</div></div><div class="text-end"><div class="transaction-amount text-success">$7,450</div><div style="font-size:.75rem;color:var(--vz-body-color);">19.6%</div></div></div>
                <div class="transaction-item mb-3"><div class="transaction-icon" style="background:var(--vz-warning-lighter);font-size:1.25rem;">🇨🇴</div><div class="transaction-info"><div class="transaction-title">Colombia</div><div class="transaction-sub">Bogotá, Medellín</div></div><div class="text-end"><div class="transaction-amount text-success">$5,320</div><div style="font-size:.75rem;color:var(--vz-body-color);">14.0%</div></div></div>
                <div class="transaction-item mb-3"><div class="transaction-icon" style="background:var(--vz-info-lighter);font-size:1.25rem;">🇲🇽</div><div class="transaction-info"><div class="transaction-title">México</div><div class="transaction-sub">Ciudad de México</div></div><div class="text-end"><div class="transaction-amount text-success">$4,710</div><div style="font-size:.75rem;color:var(--vz-body-color);">12.4%</div></div></div>
                <div class="transaction-item"><div class="transaction-icon" style="background:var(--vz-danger-lighter);font-size:1.25rem;">🇪🇸</div><div class="transaction-info"><div class="transaction-title">España</div><div class="transaction-sub">Madrid, Barcelona</div></div><div class="text-end"><div class="transaction-amount text-success">$3,280</div><div style="font-size:.75rem;color:var(--vz-body-color);">8.6%</div></div></div>
            </div>
        </div>
    </div>
</div>

<!-- Transactions + Invoice Table -->
<div class="row g-4 mb-4">
    <div class="col-xl-4 col-lg-6 animate-fadein">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div><h5 class="card-title">Transacciones</h5><p class="card-subtitle mb-0">Total 48.5% de crecimiento 😎</p></div>
                <div class="dropdown"><button class="btn btn-sm p-1" data-bs-toggle="dropdown" style="background:none;border:none;color:var(--vz-body-color);"><i class="ti ti-dots-vertical" style="font-size:1.25rem;"></i></button><ul class="dropdown-menu dropdown-menu-end"><li><a class="dropdown-item" href="#">Últimas 28 días</a></li><li><a class="dropdown-item" href="#">Mes anterior</a></li><li><a class="dropdown-item" href="#">Último año</a></li></ul></div>
            </div>
            <div class="card-body pt-1">
                <div class="transaction-item mb-3"><div class="transaction-icon" style="background:var(--vz-primary-lighter);color:var(--vz-primary);"><i class="ti ti-wallet"></i></div><div class="transaction-info"><div class="transaction-title">Pagos</div><div class="transaction-sub">Servicios prestados</div></div><div class="transaction-amount text-success">+$2,450</div></div>
                <div class="transaction-item mb-3"><div class="transaction-icon" style="background:var(--vz-success-lighter);color:var(--vz-success);"><i class="ti ti-chart-bar"></i></div><div class="transaction-info"><div class="transaction-title">Ventas</div><div class="transaction-sub">Consultoría premium</div></div><div class="transaction-amount text-success">+$14,857</div></div>
                <div class="transaction-item mb-3"><div class="transaction-icon" style="background:var(--vz-danger-lighter);color:var(--vz-danger);"><i class="ti ti-credit-card"></i></div><div class="transaction-info"><div class="transaction-title">Gastos</div><div class="transaction-sub">Operativos</div></div><div class="transaction-amount text-danger">-$1,230</div></div>
                <div class="transaction-item mb-3"><div class="transaction-icon" style="background:var(--vz-warning-lighter);color:var(--vz-warning);"><i class="ti ti-receipt"></i></div><div class="transaction-info"><div class="transaction-title">Profit</div><div class="transaction-sub">Neto del mes</div></div><div class="transaction-amount text-success">+$12,320</div></div>
                <div class="transaction-item"><div class="transaction-icon" style="background:var(--vz-info-lighter);color:var(--vz-info);"><i class="ti ti-repeat"></i></div><div class="transaction-info"><div class="transaction-title">Reembolso</div><div class="transaction-sub">Ajuste de servicio</div></div><div class="transaction-amount text-danger">-$350</div></div>
            </div>
        </div>
    </div>
    <div class="col-xl-8 col-lg-6 animate-fadein">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div><h5 class="card-title">Facturas Recientes</h5><p class="card-subtitle mb-0">Últimas operaciones</p></div>
                <a href="#" class="btn btn-sm" style="background:var(--vz-primary-lighter);color:var(--vz-primary);font-weight:600;border-radius:6px;font-size:.8125rem;">Ver Todo</a>
            </div>
            <div class="card-body pt-0" style="overflow-x:auto;">
                <table class="table-vz">
                    <thead><tr><th>#</th><th><i class="ti ti-trending-up" style="font-size:1rem;"></i></th><th>Cliente</th><th>Total</th><th>Emitida</th><th>Balance</th><th>Acción</th></tr></thead>
                    <tbody>
                        <tr><td><a href="#" class="text-primary fw-semibold">#5089</a></td><td><span class="badge-label bg-label-success"><i class="ti ti-circle-check" style="font-size:14px;"></i></span></td><td><div class="d-flex align-items-center gap-2"><div class="avatar-sm bg-label-primary">JD</div><div><div class="fw-semibold" style="font-size:.875rem;color:var(--vz-heading-color);">Juan Díaz</div><div style="font-size:.75rem;color:var(--vz-body-color);">Auditoría</div></div></div></td><td style="color:var(--vz-heading-color);font-weight:500;">$3,450</td><td>17/03/2024</td><td><span class="badge-label bg-label-success">Pagado</span></td><td><div class="d-flex gap-1"><button class="btn btn-sm p-1" style="background:none;border:none;color:var(--vz-body-color);" title="Ver"><i class="ti ti-eye"></i></button><button class="btn btn-sm p-1" style="background:none;border:none;color:var(--vz-body-color);" title="Email"><i class="ti ti-mail"></i></button><div class="dropdown d-inline"><button class="btn btn-sm p-1" data-bs-toggle="dropdown" style="background:none;border:none;color:var(--vz-body-color);"><i class="ti ti-dots-vertical"></i></button><ul class="dropdown-menu dropdown-menu-end"><li><a class="dropdown-item" href="#"><i class="ti ti-download me-2"></i>Descargar</a></li><li><a class="dropdown-item" href="#"><i class="ti ti-pencil me-2"></i>Editar</a></li><li><a class="dropdown-item text-danger" href="#"><i class="ti ti-trash me-2"></i>Eliminar</a></li></ul></div></div></td></tr>
                        <tr><td><a href="#" class="text-primary fw-semibold">#5088</a></td><td><span class="badge-label bg-label-warning"><i class="ti ti-clock" style="font-size:14px;"></i></span></td><td><div class="d-flex align-items-center gap-2"><div class="avatar-sm bg-label-success">MR</div><div><div class="fw-semibold" style="font-size:.875rem;color:var(--vz-heading-color);">María Rodríguez</div><div style="font-size:.75rem;color:var(--vz-body-color);">Consultoría</div></div></div></td><td style="color:var(--vz-heading-color);font-weight:500;">$5,200</td><td>15/03/2024</td><td><span class="badge-label bg-label-warning">Pendiente</span></td><td><div class="d-flex gap-1"><button class="btn btn-sm p-1" style="background:none;border:none;color:var(--vz-body-color);" title="Ver"><i class="ti ti-eye"></i></button><button class="btn btn-sm p-1" style="background:none;border:none;color:var(--vz-body-color);" title="Email"><i class="ti ti-mail"></i></button><div class="dropdown d-inline"><button class="btn btn-sm p-1" data-bs-toggle="dropdown" style="background:none;border:none;color:var(--vz-body-color);"><i class="ti ti-dots-vertical"></i></button><ul class="dropdown-menu dropdown-menu-end"><li><a class="dropdown-item" href="#"><i class="ti ti-download me-2"></i>Descargar</a></li><li><a class="dropdown-item" href="#"><i class="ti ti-pencil me-2"></i>Editar</a></li></ul></div></div></td></tr>
                        <tr><td><a href="#" class="text-primary fw-semibold">#5087</a></td><td><span class="badge-label bg-label-success"><i class="ti ti-circle-check" style="font-size:14px;"></i></span></td><td><div class="d-flex align-items-center gap-2"><div class="avatar-sm bg-label-danger">CG</div><div><div class="fw-semibold" style="font-size:.875rem;color:var(--vz-heading-color);">Carlos García</div><div style="font-size:.75rem;color:var(--vz-body-color);">Procesos</div></div></div></td><td style="color:var(--vz-heading-color);font-weight:500;">$2,100</td><td>12/03/2024</td><td><span class="badge-label bg-label-success">Pagado</span></td><td><div class="d-flex gap-1"><button class="btn btn-sm p-1" style="background:none;border:none;color:var(--vz-body-color);" title="Ver"><i class="ti ti-eye"></i></button><button class="btn btn-sm p-1" style="background:none;border:none;color:var(--vz-body-color);" title="Email"><i class="ti ti-mail"></i></button><div class="dropdown d-inline"><button class="btn btn-sm p-1" data-bs-toggle="dropdown" style="background:none;border:none;color:var(--vz-body-color);"><i class="ti ti-dots-vertical"></i></button><ul class="dropdown-menu dropdown-menu-end"><li><a class="dropdown-item" href="#"><i class="ti ti-download me-2"></i>Descargar</a></li><li><a class="dropdown-item" href="#"><i class="ti ti-pencil me-2"></i>Editar</a></li></ul></div></div></td></tr>
                        <tr><td><a href="#" class="text-primary fw-semibold">#5086</a></td><td><span class="badge-label bg-label-danger"><i class="ti ti-alert-circle" style="font-size:14px;"></i></span></td><td><div class="d-flex align-items-center gap-2"><div class="avatar-sm bg-label-warning">AL</div><div><div class="fw-semibold" style="font-size:.875rem;color:var(--vz-heading-color);">Ana López</div><div style="font-size:.75rem;color:var(--vz-body-color);">Estructuración</div></div></div></td><td style="color:var(--vz-heading-color);font-weight:500;">$7,800</td><td>10/03/2024</td><td><span class="badge-label bg-label-danger">Vencida</span></td><td><div class="d-flex gap-1"><button class="btn btn-sm p-1" style="background:none;border:none;color:var(--vz-body-color);" title="Ver"><i class="ti ti-eye"></i></button><button class="btn btn-sm p-1" style="background:none;border:none;color:var(--vz-body-color);" title="Email"><i class="ti ti-mail"></i></button><div class="dropdown d-inline"><button class="btn btn-sm p-1" data-bs-toggle="dropdown" style="background:none;border:none;color:var(--vz-body-color);"><i class="ti ti-dots-vertical"></i></button><ul class="dropdown-menu dropdown-menu-end"><li><a class="dropdown-item" href="#"><i class="ti ti-download me-2"></i>Descargar</a></li><li><a class="dropdown-item" href="#"><i class="ti ti-pencil me-2"></i>Editar</a></li></ul></div></div></td></tr>
                        <tr><td><a href="#" class="text-primary fw-semibold">#5085</a></td><td><span class="badge-label bg-label-success"><i class="ti ti-circle-check" style="font-size:14px;"></i></span></td><td><div class="d-flex align-items-center gap-2"><div class="avatar-sm bg-label-info">PM</div><div><div class="fw-semibold" style="font-size:.875rem;color:var(--vz-heading-color);">Pedro Martínez</div><div style="font-size:.75rem;color:var(--vz-body-color);">Auditoría</div></div></div></td><td style="color:var(--vz-heading-color);font-weight:500;">$4,150</td><td>08/03/2024</td><td><span class="badge-label bg-label-success">Pagado</span></td><td><div class="d-flex gap-1"><button class="btn btn-sm p-1" style="background:none;border:none;color:var(--vz-body-color);" title="Ver"><i class="ti ti-eye"></i></button><button class="btn btn-sm p-1" style="background:none;border:none;color:var(--vz-body-color);" title="Email"><i class="ti ti-mail"></i></button><div class="dropdown d-inline"><button class="btn btn-sm p-1" data-bs-toggle="dropdown" style="background:none;border:none;color:var(--vz-body-color);"><i class="ti ti-dots-vertical"></i></button><ul class="dropdown-menu dropdown-menu-end"><li><a class="dropdown-item" href="#"><i class="ti ti-download me-2"></i>Descargar</a></li><li><a class="dropdown-item" href="#"><i class="ti ti-pencil me-2"></i>Editar</a></li></ul></div></div></td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const P='#7367F0',S='#28C76F',D='#EA5455',W='#FF9F43',I='#00CFE8',BC='#6F6B7D',LC='#5D596C';
    const miniOpts=(color,data)=>({series:[{data}],chart:{height:50,width:100,type:'line',sparkline:{enabled:true}},stroke:{width:2,curve:'smooth'},colors:[color],tooltip:{enabled:false}});
    new ApexCharts(document.querySelector('#miniChart1'),miniOpts(P,[20,40,30,50,45,60,55])).render();
    new ApexCharts(document.querySelector('#miniChart2'),miniOpts(S,[30,25,45,35,55,40,60])).render();
    new ApexCharts(document.querySelector('#miniChart3'),miniOpts(W,[45,35,40,30,25,35,20])).render();
    new ApexCharts(document.querySelector('#miniChart4'),miniOpts(I,[15,35,25,45,50,55,65])).render();

    new ApexCharts(document.querySelector('#revenueChart'),{series:[{name:'Ingreso',type:'column',data:[95,177,284,256,105,63,168,218,72,120,160,230]},{name:'Gastos',type:'line',data:[30,50,75,60,42,38,55,78,35,55,48,85]}],chart:{height:280,type:'line',toolbar:{show:false},fontFamily:"'Public Sans',sans-serif"},plotOptions:{bar:{borderRadius:8,columnWidth:'35%'}},colors:[P,W],stroke:{width:[0,3],curve:'smooth'},fill:{opacity:[1,1]},grid:{borderColor:'#F1F0F2',strokeDashArray:4,padding:{top:-10,bottom:-8}},xaxis:{categories:['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'],axisBorder:{show:false},axisTicks:{show:false},labels:{style:{fontSize:'12px',colors:BC}}},yaxis:{labels:{style:{fontSize:'12px',colors:BC}}},legend:{show:true,position:'top',horizontalAlign:'left',fontSize:'13px',markers:{width:10,height:10,radius:50},labels:{colors:BC}},dataLabels:{enabled:false},tooltip:{theme:'light'}}).render();

    new ApexCharts(document.querySelector('#weeklyEarningChart'),{series:[78],chart:{height:100,type:'radialBar',sparkline:{enabled:true}},plotOptions:{radialBar:{hollow:{size:'55%'},track:{background:'#F1F0F2'},dataLabels:{name:{show:false},value:{offsetY:6,fontSize:'16px',fontWeight:600,color:LC}}}},colors:[P],states:{hover:{filter:{type:'none'}}}}).render();

    new ApexCharts(document.querySelector('#orderStatsDonut'),{series:[85,25,50,40],chart:{height:150,type:'donut'},labels:['Auditoría','Consultoría','Procesos','Estructuración'],colors:[P,S,W,I],plotOptions:{pie:{donut:{size:'70%',labels:{show:true,name:{fontSize:'12px',color:BC},value:{fontSize:'18px',fontWeight:700,color:LC,formatter:v=>v+'k'},total:{show:true,label:'Total',fontSize:'12px',color:BC,formatter:()=>'42.8k'}}}}},legend:{show:false},dataLabels:{enabled:false},stroke:{width:3,colors:['var(--vz-card-bg)']},states:{hover:{filter:{type:'none'}}},tooltip:{theme:'light'}}).render();

    new ApexCharts(document.querySelector('#earningBarChart'),{series:[{name:'Ganancias',data:[28,40,36,52,38,60,55]}],chart:{height:200,type:'bar',toolbar:{show:false},fontFamily:"'Public Sans',sans-serif"},plotOptions:{bar:{borderRadius:7,columnWidth:'50%',distributed:true}},colors:['rgba(115,103,240,0.16)','rgba(115,103,240,0.16)','rgba(115,103,240,0.16)',P,'rgba(115,103,240,0.16)','rgba(115,103,240,0.16)','rgba(115,103,240,0.16)'],grid:{borderColor:'#F1F0F2',strokeDashArray:4,padding:{top:-15,bottom:-10}},xaxis:{categories:['Lun','Mar','Mié','Jue','Vie','Sáb','Dom'],axisBorder:{show:false},axisTicks:{show:false},labels:{style:{fontSize:'12px',colors:BC}}},yaxis:{show:false},legend:{show:false},dataLabels:{enabled:false},tooltip:{theme:'light'}}).render();

    new ApexCharts(document.querySelector('#supportTrackerChart'),{series:[85],chart:{height:150,type:'radialBar'},plotOptions:{radialBar:{hollow:{size:'60%'},startAngle:-140,endAngle:130,track:{background:'#F1F0F2',strokeWidth:'100%'},dataLabels:{name:{offsetY:-12,fontSize:'12px',fontWeight:400,color:BC},value:{offsetY:4,fontSize:'22px',fontWeight:700,color:LC}}}},fill:{type:'gradient',gradient:{shade:'dark',type:'horizontal',shadeIntensity:0.5,gradientToColors:[P],stops:[0,100]}},colors:[P],labels:['Completados'],states:{hover:{filter:{type:'none'}}}}).render();
});
</script>
@endpush
