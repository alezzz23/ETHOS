<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Propuesta de Servicio #{{ $proposal->id }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'DejaVu Sans', Arial, sans-serif; font-size: 11px; color: #2c3e50; line-height: 1.5; }
        .page { padding: 30px 40px; }
        /* Header */
        .header { display: flex; justify-content: space-between; align-items: center; border-bottom: 3px solid #1a3c5e; padding-bottom: 16px; margin-bottom: 24px; }
        .logo-block h1 { font-size: 22px; font-weight: 700; color: #1a3c5e; letter-spacing: 2px; }
        .logo-block p  { font-size: 10px; color: #7f8c8d; margin-top: 2px; }
        .meta-block { text-align: right; }
        .meta-block .badge { display: inline-block; background: #1a3c5e; color: #fff; padding: 3px 10px; border-radius: 20px; font-size: 10px; font-weight: 600; margin-bottom: 4px; }
        .meta-block p { font-size: 10px; color: #7f8c8d; }
        /* Section */
        .section { margin-bottom: 22px; }
        .section-title { font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: #1a3c5e; border-bottom: 1px solid #e0e5ef; padding-bottom: 6px; margin-bottom: 12px; }
        /* Info grid */
        .info-grid { display: table; width: 100%; border-collapse: collapse; }
        .info-row  { display: table-row; }
        .info-label, .info-value { display: table-cell; padding: 5px 8px; vertical-align: top; }
        .info-label { width: 40%; font-weight: 600; color: #5a6a7e; font-size: 10px; }
        .info-value { color: #2c3e50; font-size: 11px; }
        .info-row:nth-child(even) .info-label,
        .info-row:nth-child(even) .info-value { background: #f8f9fc; }
        /* Price highlight */
        .price-box { background: linear-gradient(135deg, #1a3c5e 0%, #2980b9 100%); color: #fff; border-radius: 6px; padding: 16px 20px; margin-bottom: 16px; }
        .price-box .label { font-size: 10px; text-transform: uppercase; letter-spacing: 1px; opacity: .8; margin-bottom: 4px; }
        .price-box .amount { font-size: 24px; font-weight: 700; }
        .price-box .sub { font-size: 10px; opacity: .7; margin-top: 2px; }
        /* Hours breakdown table */
        table.breakdown { width: 100%; border-collapse: collapse; font-size: 10px; }
        table.breakdown th { background: #1a3c5e; color: #fff; padding: 7px 10px; text-align: left; }
        table.breakdown td { padding: 6px 10px; border-bottom: 1px solid #e8ecf0; }
        table.breakdown tr:nth-child(even) td { background: #f8f9fc; }
        table.breakdown tfoot td { font-weight: 700; border-top: 2px solid #1a3c5e; }
        /* Milestones */
        .milestone-item { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #eee; font-size: 10px; }
        .milestone-item:last-child { border-bottom: none; }
        /* Footer */
        .footer { margin-top: 40px; border-top: 1px solid #e0e5ef; padding-top: 12px; text-align: center; font-size: 9px; color: #95a5a6; }
    </style>
</head>
<body>
<div class="page">

    {{-- Header --}}
    <div class="header">
        <div class="logo-block">
            <h1>ETHOS</h1>
            <p>Consultoría & Gestión Empresarial</p>
        </div>
        <div class="meta-block">
            <div class="badge">Propuesta #{{ str_pad($proposal->id, 5, '0', STR_PAD_LEFT) }}</div>
            <p>Fecha: {{ now()->format('d/m/Y') }}</p>
        </div>
    </div>

    {{-- Client / Project --}}
    <div class="section">
        <div class="section-title">Información del Cliente</div>
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Empresa:</div>
                <div class="info-value">{{ $proposal->project->client?->name ?? '—' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Proyecto:</div>
                <div class="info-value">{{ $proposal->project->title }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Servicio:</div>
                <div class="info-value">{{ $proposal->service?->short_name ?? '—' }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Tamaño de empresa:</div>
                <div class="info-value">{{ ucfirst($proposal->client_size) }}</div>
            </div>
            <div class="info-row">
                <div class="info-label">Consultor:</div>
                <div class="info-value">{{ $proposal->createdBy->name }}</div>
            </div>
        </div>
    </div>

    {{-- Price highlight --}}
    <div class="section">
        <div class="section-title">Inversión Estimada</div>
        <div class="price-box">
            <div class="label">Rango de precio</div>
            <div class="amount">$ {{ number_format($proposal->price_min, 2) }} — $ {{ number_format($proposal->price_max, 2) }}</div>
            <div class="sub">
                {{ $proposal->adjusted_hours }} horas estimadas &bull;
                Tasa: ${{ number_format($proposal->hourly_rate, 2) }}/h &bull;
                Margen: {{ $proposal->margin_percent }}%
            </div>
        </div>
        @if($proposal->adjustment_reason)
        <p style="font-size:10px;color:#7f8c8d;margin-top:6px;"><strong>Nota de ajuste:</strong> {{ $proposal->adjustment_reason }}</p>
        @endif
    </div>

    {{-- Payment milestones --}}
    @if($proposal->payment_milestones)
    <div class="section">
        <div class="section-title">Plan de Pagos</div>
        @foreach($proposal->payment_milestones as $milestone)
        <div class="milestone-item">
            <span>{{ $milestone['label'] ?? 'Hito' }}</span>
            <span>{{ $milestone['percentage'] ?? 0 }}% — $ {{ number_format(($proposal->price_min + $proposal->price_max) / 2 * ($milestone['percentage'] ?? 0) / 100, 2) }}</span>
        </div>
        @endforeach
    </div>
    @endif

    {{-- Service description --}}
    @if($proposal->service?->description)
    <div class="section">
        <div class="section-title">Descripción del Servicio</div>
        <p style="font-size:10px;line-height:1.7;">{{ $proposal->service->description }}</p>
    </div>
    @endif

    {{-- Terms --}}
    <div class="section">
        <div class="section-title">Términos y Condiciones</div>
        <p style="font-size:9px;color:#7f8c8d;line-height:1.7;">
            Esta propuesta tiene una validez de 30 días calendario desde su fecha de emisión. Los precios están expresados en dólares americanos (USD) y no incluyen impuestos aplicables. ETHOS se reserva el derecho de ajustar el alcance del proyecto ante cambios relevantes en los requerimientos del cliente. La aprobación de esta propuesta implica la aceptación de los presentes términos.
        </p>
    </div>

    {{-- Footer --}}
    <div class="footer">
        ETHOS Consultoría &bull; {{ now()->year }} &bull; Propuesta generada el {{ now()->format('d/m/Y H:i') }}
    </div>

</div>
</body>
</html>
