@extends('layouts.vuexy')

@section('title', 'Mapa de Clientes')

@section('content')
@php
    $clientLat = $client->latitude;
    $clientLng = $client->longitude;
@endphp

<div class="row">
    <div class="col-12">
        <div class="card ethos-crm-card">
            <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div class="d-flex align-items-center gap-2">
                    <i class="ti ti-map"></i>
                    <div>
                        <h5 class="mb-0">Mapa de Clientes</h5>
                        <small class="text-muted">{{ $client->name }}</small>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('clients.index') }}" class="btn btn-label-secondary">
                        <i class="ti ti-arrow-left"></i>
                        <span>Volver</span>
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-12 col-lg-4">
                        <div class="card border shadow-none h-100">
                            <div class="card-body">
                                <h6 class="mb-3 d-flex align-items-center gap-2">
                                    <i class="ti ti-filter"></i>
                                    <span>Filtros</span>
                                </h6>

                                <div class="row g-2">
                                    <div class="col-12">
                                        <label class="form-label">Tipo de contacto</label>
                                        <select class="form-select" id="mapFilterContactType">
                                            <option value="">Todos</option>
                                            <option value="primary">Contacto principal</option>
                                            <option value="secondary">Contacto secundario</option>
                                        </select>
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label">Estado</label>
                                        <input type="text" class="form-control" id="mapFilterStatus" placeholder="lead, prospecto, cliente...">
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label">Tipo de negocio</label>
                                        <input type="text" class="form-control" id="mapFilterType" placeholder="empresa, gobierno...">
                                    </div>

                                    <div class="col-12 d-grid">
                                        <button type="button" class="btn btn-primary" id="mapApplyFiltersBtn">
                                            <i class="ti ti-refresh"></i>
                                            <span>Aplicar</span>
                                        </button>
                                    </div>

                                    <div class="col-12">
                                        <div class="alert d-none" id="mapFeedback" role="alert" aria-live="polite"></div>
                                    </div>
                                </div>

                                <hr class="my-3">

                                <h6 class="mb-2 d-flex align-items-center gap-2">
                                    <i class="ti ti-pin"></i>
                                    <span>Ubicación del cliente</span>
                                </h6>
                                <div class="small text-muted">
                                    @if($clientLat && $clientLng)
                                        <div><strong>Lat:</strong> {{ $clientLat }}</div>
                                        <div><strong>Lng:</strong> {{ $clientLng }}</div>
                                    @else
                                        <div>Este cliente no tiene coordenadas registradas.</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-lg-8">
                        <div class="card border shadow-none">
                            <div class="card-body p-0">
                                <div id="clientsMap" style="width:100%; height: min(70vh, 640px);"></div>
                            </div>
                        </div>
                        <div class="mt-2 small text-muted d-flex align-items-center gap-2">
                            <i class="ti ti-info-circle"></i>
                            <span>Arrastra el mapa para panear, usa la rueda o los controles para zoom, y toca los marcadores para ver información.</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(() => {
    const apiKey = @json($googleMapsApiKey);
    const mapId = @json($googleMapsMapId);
    const currentClientId = @json($client->id);
    const currentClientLat = @json($client->latitude);
    const currentClientLng = @json($client->longitude);

    const mapEl = document.getElementById('clientsMap');
    const feedbackEl = document.getElementById('mapFeedback');

    const contactTypeEl = document.getElementById('mapFilterContactType');
    const statusEl = document.getElementById('mapFilterStatus');
    const typeEl = document.getElementById('mapFilterType');
    const applyBtn = document.getElementById('mapApplyFiltersBtn');

    const showFeedback = (type, message) => {
        if (!feedbackEl) return;
        feedbackEl.classList.remove('d-none', 'alert-success', 'alert-danger', 'alert-warning');
        feedbackEl.classList.add(type === 'success' ? 'alert-success' : type === 'warning' ? 'alert-warning' : 'alert-danger');
        feedbackEl.textContent = message;
    };

    const clearFeedback = () => {
        if (!feedbackEl) return;
        feedbackEl.classList.add('d-none');
        feedbackEl.classList.remove('alert-success', 'alert-danger', 'alert-warning');
        feedbackEl.textContent = '';
    };

    const escapeHtml = (value) => {
        return String(value ?? '')
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');
    };

    const loadScriptOnce = () => {
        if (window.google && window.google.maps) {
            return Promise.resolve();
        }

        if (!apiKey) {
            showFeedback('danger', 'Falta configurar GOOGLE_MAPS_API_KEY.');
            return Promise.reject(new Error('Missing Google Maps API key'));
        }

        return new Promise((resolve, reject) => {
            if (document.getElementById('googleMapsScript')) {
                return resolve();
            }
            const s = document.createElement('script');
            s.id = 'googleMapsScript';
            s.async = true;
            s.defer = true;
            s.src = `https://maps.googleapis.com/maps/api/js?key=${encodeURIComponent(apiKey)}&v=weekly`;
            s.onload = () => resolve();
            s.onerror = () => reject(new Error('Failed to load Google Maps'));
            document.head.appendChild(s);
        });
    };

    let map = null;
    let infoWindow = null;
    let markers = [];

    const clearMarkers = () => {
        markers.forEach(m => m.setMap(null));
        markers = [];
    };

    const markerInfoHtml = (m) => {
        const title = escapeHtml(m.title || 'Cliente');
        const type = escapeHtml(m.type || '');
        const status = escapeHtml(m.status || '');
        const addr = escapeHtml(m.address || '');
        const primary = escapeHtml(m.contact?.primary || '');
        const secondary = escapeHtml(m.contact?.secondary || '');

        const url = m.url ? `<a href="${escapeHtml(m.url)}" class="btn btn-sm btn-primary" style="margin-top:0.5rem;">Ver</a>` : '';

        return `
            <div style="max-width:260px;">
                <div style="font-weight:600; margin-bottom:0.25rem;">${title}</div>
                ${type ? `<div class="text-muted" style="font-size:0.85rem;">Tipo: ${type}</div>` : ''}
                ${status ? `<div class="text-muted" style="font-size:0.85rem;">Estado: ${status}</div>` : ''}
                ${addr ? `<div style="margin-top:0.35rem; font-size:0.9rem;">${addr}</div>` : ''}
                ${(primary || secondary) ? `
                    <div style="margin-top:0.35rem; font-size:0.85rem;">
                        ${primary ? `<div><strong>Principal:</strong> ${primary}</div>` : ''}
                        ${secondary ? `<div><strong>Secundario:</strong> ${secondary}</div>` : ''}
                    </div>
                ` : ''}
                ${url}
            </div>
        `;
    };

    const fetchMarkers = async () => {
        const params = new URLSearchParams();
        const contactType = contactTypeEl?.value || '';
        const status = (statusEl?.value || '').trim();
        const type = (typeEl?.value || '').trim();

        if (contactType) params.set('contact_type', contactType);
        if (status) params.set('status', status);
        if (type) params.set('type', type);

        const res = await fetch(`/admin/clients/markers?${params.toString()}`, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        if (!res.ok) {
            throw new Error('No se pudieron cargar los marcadores.');
        }

        const data = await res.json();
        return Array.isArray(data.markers) ? data.markers : [];
    };

    const renderMarkers = (items) => {
        clearMarkers();

        items.forEach((m) => {
            const pos = m.position;
            if (!pos || typeof pos.lat !== 'number' || typeof pos.lng !== 'number') {
                return;
            }

            const isCurrent = Number(m.id) === Number(currentClientId);
            const marker = new google.maps.Marker({
                position: pos,
                map,
                title: m.title || '',
                label: isCurrent ? { text: '★', color: '#ffffff' } : undefined,
            });

            marker.addListener('click', () => {
                infoWindow.setContent(markerInfoHtml(m));
                infoWindow.open({ map, anchor: marker });
            });

            markers.push(marker);
        });

        if (markers.length === 0) {
            showFeedback('warning', 'No hay clientes con coordenadas para los filtros seleccionados.');
        }
    };

    const fitMap = () => {
        if (!map) return;

        const bounds = new google.maps.LatLngBounds();
        let hasAny = false;

        markers.forEach((mk) => {
            const p = mk.getPosition();
            if (p) {
                bounds.extend(p);
                hasAny = true;
            }
        });

        if (currentClientLat && currentClientLng) {
            bounds.extend({ lat: Number(currentClientLat), lng: Number(currentClientLng) });
            hasAny = true;
        }

        if (hasAny) {
            map.fitBounds(bounds);
            const listener = google.maps.event.addListenerOnce(map, 'bounds_changed', () => {
                if (map.getZoom() > 16) {
                    map.setZoom(16);
                }
            });
            if (listener) {
                google.maps.event.removeListener(listener);
            }
        }
    };

    const loadAndRender = async () => {
        clearFeedback();
        applyBtn && (applyBtn.disabled = true);

        try {
            const items = await fetchMarkers();
            renderMarkers(items);
            fitMap();
        } catch (e) {
            showFeedback('danger', e?.message || 'Error al cargar el mapa.');
        } finally {
            applyBtn && (applyBtn.disabled = false);
        }
    };

    const init = async () => {
        if (!mapEl) {
            return;
        }

        await loadScriptOnce();

        const defaultCenter = (currentClientLat && currentClientLng)
            ? { lat: Number(currentClientLat), lng: Number(currentClientLng) }
            : { lat: 10.4806, lng: -66.9036 };

        map = new google.maps.Map(mapEl, {
            center: defaultCenter,
            zoom: 12,
            mapId: mapId || undefined,
            gestureHandling: 'greedy',
            fullscreenControl: true,
            streetViewControl: false,
            mapTypeControl: false,
        });

        infoWindow = new google.maps.InfoWindow();

        await loadAndRender();

        applyBtn?.addEventListener('click', () => loadAndRender());
    };

    init().catch(() => {});
})();
</script>
@endpush
