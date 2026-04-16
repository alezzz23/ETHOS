@extends('layouts.vuexy')
@section('title', 'Uso de IA — Observabilidad')

@section('content')
<x-ethos.page-header
    eyebrow="Observabilidad"
    title="Uso de IA"
    subtitle="Consumo de tokens, costos estimados, modelos y feedback del chatbot administrativo." />

<div class="row g-3 mb-3" id="aiUsageStats">
    <div class="col-md-3">
        <x-ethos.stat-card title="Mensajes" :value="$summary['total_messages']" icon="ri-chat-3-line" />
    </div>
    <div class="col-md-3">
        <x-ethos.stat-card title="Tokens usados" :value="number_format($summary['total_tokens'])" icon="ri-cpu-line" />
    </div>
    <div class="col-md-3">
        <x-ethos.stat-card title="Usuarios únicos" :value="$summary['unique_users']" icon="ri-user-line" />
    </div>
    <div class="col-md-3">
        <x-ethos.stat-card title="Conversaciones" :value="$summary['conversations']" icon="ri-message-2-line" />
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-8">
        <x-ethos.section title="Tokens por día" subtitle="Últimos 30 días">
            <div id="chartTokensByDay" style="min-height: 320px;"></div>
        </x-ethos.section>
    </div>
    <div class="col-lg-4">
        <x-ethos.section title="Modelos" subtitle="Distribución de tokens">
            <div id="chartTopModels" style="min-height: 320px;"></div>
        </x-ethos.section>
    </div>

    <div class="col-lg-6">
        <x-ethos.section title="Top 10 usuarios" subtitle="Por tokens consumidos">
            <div class="table-responsive">
                <table class="table table-sm align-middle mb-0">
                    <thead><tr><th>#</th><th>Usuario</th><th class="text-end">Tokens</th><th class="text-end">Mensajes</th></tr></thead>
                    <tbody id="tblTopUsers"><tr><td colspan="4" class="text-muted">Cargando…</td></tr></tbody>
                </table>
            </div>
        </x-ethos.section>
    </div>

    <div class="col-lg-6">
        <x-ethos.section title="Feedback del usuario" subtitle="Ratio 👍 vs 👎">
            <div id="chartFeedback" style="min-height: 260px;"></div>
            <div class="text-end mt-2">
                <small class="text-muted">Costo estimado del periodo: <strong id="costEstimate">—</strong> USD</small>
            </div>
        </x-ethos.section>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function(){
    const endpoint = @json(route('admin.ai-usage.data'));

    async function load(){
        const res = await fetch(endpoint + '?days=30', { headers: { 'Accept': 'application/json' } });
        if (!res.ok) return;
        const d = await res.json();

        // Tokens por día (línea)
        const dayCats = d.tokens_by_day.map(r => r.day);
        const dayVals = d.tokens_by_day.map(r => r.tokens);
        new ApexCharts(document.querySelector('#chartTokensByDay'), {
            chart: { type: 'area', height: 320, toolbar: { show: false }, animations: { easing: 'easeout', speed: 500 } },
            series: [{ name: 'Tokens', data: dayVals }],
            xaxis: { categories: dayCats, labels: { rotate: -45 } },
            stroke: { width: 2, curve: 'smooth' },
            dataLabels: { enabled: false },
            colors: ['#4f46e5'],
            fill: { type: 'gradient', gradient: { opacityFrom: 0.4, opacityTo: 0.05 } },
        }).render();

        // Modelos (donut)
        new ApexCharts(document.querySelector('#chartTopModels'), {
            chart: { type: 'donut', height: 320 },
            series: d.top_models.map(r => r.tokens),
            labels: d.top_models.map(r => r.model),
            legend: { position: 'bottom' },
        }).render();

        // Top usuarios (tabla)
        const tbody = document.getElementById('tblTopUsers');
        tbody.innerHTML = d.top_users.length
            ? d.top_users.map((u, i) => `<tr>
                    <td>${i+1}</td>
                    <td>${u.name}</td>
                    <td class="text-end">${u.tokens.toLocaleString()}</td>
                    <td class="text-end">${u.msgs}</td>
                </tr>`).join('')
            : '<tr><td colspan="4" class="text-muted text-center">Sin datos.</td></tr>';

        // Feedback (bar horizontal)
        new ApexCharts(document.querySelector('#chartFeedback'), {
            chart: { type: 'bar', height: 260, toolbar: { show: false } },
            series: [{ name: 'Cantidad', data: [d.feedback_ratio.helpful, d.feedback_ratio.not_helpful] }],
            xaxis: { categories: ['👍 Útil', '👎 No útil'] },
            plotOptions: { bar: { horizontal: true, borderRadius: 6 } },
            colors: ['#16a34a'],
            dataLabels: { enabled: true },
        }).render();

        document.getElementById('costEstimate').textContent = (d.cost_estimate || 0).toFixed(4);
    }

    if (window.ApexCharts) { load(); }
    else { document.addEventListener('DOMContentLoaded', load); }
})();
</script>
@endpush
