<!DOCTYPE html>
<html lang="es" class="light-style layout-menu-fixed layout-navbar-fixed" dir="ltr" data-theme="theme-default">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') - ETHOS Admin</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    {{-- ETHOS typographic pair: Fraunces (display serif) · Inter Tight (UI) · JetBrains Mono (numeric/tabular) --}}
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400;9..144,500;9..144,600;9..144,700;9..144,800&family=Inter+Tight:wght@400;500;600;700&family=JetBrains+Mono:wght@500;600;700&display=swap" rel="stylesheet">
    {{-- Brand mark only (logo, login wordmark) — no longer used as generic display font. --}}
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@2.44.0/tabler-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/apexcharts@3.44.0/dist/apexcharts.css">
    <link rel="stylesheet" href="{{ asset('css/vuexy.css') }}">
    <link rel="stylesheet" href="{{ asset('css/ethos-components.css') }}">
    <link rel="stylesheet" href="{{ asset('css/ethos-typography.css') }}">
    {{-- ETHOS AI Chatbot: marked + DOMPurify se bundelean ahora vía Vite en resources/js/admin/chat.js (Fase 2). --}}
    <style>
    /* ─── Notification Bell ───────────────────────────────────────── */
    .ethos-notif-badge {
        position: absolute;
        top: 4px; right: 4px;
        min-width: 17px; height: 17px;
        padding: 0 4px;
        border-radius: 999px;
        background: #e11d48;
        color: #fff;
        font-size: .7rem;
        font-weight: 700;
        line-height: 17px;
        text-align: center;
        pointer-events: none;
    }
    .ethos-notif-panel {
        width: 360px;
        border-radius: 12px;
        box-shadow: 0 8px 32px rgba(0,0,0,.14);
        border: 1px solid var(--vz-border-color, #e9ecef);
        background: var(--vz-body-bg, #fff);
    }
    .ethos-notif-header {
        border-bottom: 1px solid var(--vz-border-color, #e9ecef);
        background: var(--vz-secondary-bg, #f8f9fa);
        border-radius: 12px 12px 0 0;
    }
    .ethos-notif-item {
        display: flex;
        align-items: flex-start;
        gap: .75rem;
        padding: .75rem 1rem;
        cursor: pointer;
        border-bottom: 1px solid var(--vz-border-color, #f1f1f1);
        transition: background .15s;
        text-decoration: none;
        color: inherit;
    }
    .ethos-notif-item:hover { background: var(--vz-secondary-bg, #f8faff); }
    .ethos-notif-item.unread { background: rgba(99,102,241,.05); }
    .ethos-notif-item.unread:hover { background: rgba(99,102,241,.1); }
    .ethos-notif-dot {
        width: 8px; height: 8px;
        border-radius: 50%;
        background: #6366f1;
        flex-shrink: 0;
        margin-top: 6px;
    }
    .ethos-notif-dot.read { background: transparent; border: 1px solid var(--vz-border-color,#dee2e6); }
    .ethos-notif-title { font-size: .85rem; font-weight: 600; color: var(--vz-heading-color, #333); margin-bottom: 2px; }
    .ethos-notif-msg   { font-size: .78rem; color: var(--vz-secondary-color, #6c757d); line-height: 1.4; }
    .ethos-notif-time  { font-size: .7rem; color: var(--vz-secondary-color, #adb5bd); white-space: nowrap; flex-shrink: 0; }
    .ethos-notif-footer { background: var(--vz-secondary-bg, #f8f9fa); border-radius: 0 0 12px 12px; }
    </style>
    @stack('styles')
    <script>
        (function() {
            var theme = localStorage.getItem('theme');
            if (theme === 'dark' || (!theme && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.remove('light-style');
                document.documentElement.classList.add('dark-style');
            }
        })();
    </script>
</head>
<body>
    <a href="#mainContent" class="skip-link">Saltar al contenido</a>
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Search Modal -->
    <div class="search-modal" id="searchModal">
        <div class="search-modal-content">
            <div class="search-modal-header">
                <i class="ti ti-search"></i>
                <input type="text" id="searchModalInput" placeholder="Buscar clientes, proyectos, navegación..." autocomplete="off" aria-label="Buscar" aria-controls="searchResults" aria-expanded="false">
                <kbd>ESC</kbd>
            </div>
            <div class="search-modal-body" id="searchResults">
                <div class="d-flex align-items-center justify-content-between gap-2" style="padding:0.25rem 0.25rem 0.75rem; border-bottom: 1px solid var(--vz-border-color);">
                    <div class="btn-group" role="group" aria-label="Filtros de búsqueda">
                        <button type="button" class="btn btn-sm btn-outline-primary search-filter-btn active" data-category="all">Todo</button>
                        <button type="button" class="btn btn-sm btn-outline-primary search-filter-btn" data-category="clients">Clientes</button>
                        <button type="button" class="btn btn-sm btn-outline-primary search-filter-btn" data-category="projects">Proyectos</button>
                        <button type="button" class="btn btn-sm btn-outline-primary search-filter-btn" data-category="navigation">Navegación</button>
                    </div>
                    <button type="button" class="btn btn-sm btn-label-secondary" id="searchClearHistoryBtn" aria-label="Borrar historial">Borrar</button>
                </div>

                <div id="searchStatus" class="text-muted" style="font-size:0.85rem; padding:0.75rem 0.25rem;"></div>
                <div id="searchRecentSection" style="display:none;">
                    <div class="search-section-title">Búsquedas recientes</div>
                    <div id="searchRecentList"></div>
                </div>
                <div id="searchSuggestionsSection" style="display:none;">
                    <div class="search-section-title">Sugerencias</div>
                    <div id="searchSuggestionsList" role="listbox" aria-label="Sugerencias"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <aside class="layout-menu" id="layoutMenu">
        <div class="app-brand">
            <a href="/admin/dashboard" class="app-brand-link">
                <span class="brand-text">ETHOS</span>
            </a>
            <button class="sidebar-toggler d-none d-xl-block" id="sidebarCollapse" title="Toggle sidebar" aria-label="Alternar barra lateral">
                <i class="ti ti-circle-dot"></i>
            </button>
            <button class="sidebar-close d-xl-none" id="sidebarClose" aria-label="Cerrar barra lateral"><i class="ti ti-x"></i></button>
        </div>
        <ul class="menu-inner" style="list-style:none;padding:0;">
            <li class="menu-header">Dashboards</li>
            <li class="menu-item"><a href="/admin/dashboard" class="menu-link {{ request()->is('admin/dashboard') ? 'active' : '' }}"><i class="ti ti-smart-home"></i><span>Inicio</span></a></li>
            @can('clients.view')
            <li class="menu-item"><a href="/admin/clients" class="menu-link {{ request()->is('admin/clients*') ? 'active' : '' }}"><i class="ti ti-users"></i><span>Clientes</span></a></li>
            @endcan
            @can('projects.view')
            <li class="menu-item"><a href="/admin/projects" class="menu-link {{ request()->is('admin/projects*') ? 'active' : '' }}"><i class="ti ti-briefcase"></i><span>Proyectos</span></a></li>
            @endcan
            @can('services.view')
            <li class="menu-item"><a href="{{ route('services.index') }}" class="menu-link {{ request()->is('admin/services*') ? 'active' : '' }}"><i class="ti ti-tools"></i><span>Servicios</span></a></li>
            @endcan
            @can('users.manage')
            <li class="menu-item"><a href="{{ route('users.index') }}" class="menu-link {{ request()->is('admin/users*') ? 'active' : '' }}"><i class="ti ti-users-group"></i><span>Usuarios</span></a></li>
            @endcan
            @can('admin.access')
            <li class="menu-item"><a href="{{ route('restricted-topics.index') }}" class="menu-link {{ request()->is('admin/restricted-topics*') ? 'active' : '' }}"><i class="ti ti-shield-lock"></i><span>Tópicos Restringidos</span></a></li>
            @endcan
            @can('proposals.view')
            <li class="menu-item"><a href="{{ route('proposals.index') }}" class="menu-link {{ request()->is('admin/proposals*') ? 'active' : '' }}"><i class="ti ti-file-description"></i><span>Propuestas</span></a></li>
            <li class="menu-item"><a href="{{ route('checklists.index') }}" class="menu-link {{ request()->is('admin/checklists*') ? 'active' : '' }}"><i class="ti ti-list-check"></i><span>Levantamiento</span></a></li>
            @endcan
            @can('admin.access')
            <li class="menu-item"><a href="{{ route('knowledge-base.dashboard') }}" class="menu-link {{ request()->is('admin/knowledge-base*') ? 'active' : '' }}"><i class="ti ti-brain"></i><span>Base de Conocimiento</span></a></li>
            @endcan
         <!--    <li class="menu-header">Apps & Pages</li>
            <li class="menu-item"><a href="#" class="menu-link"><i class="ti ti-mail"></i><span>Email</span><span class="menu-badge badge-label bg-label-primary ms-auto">12</span></a></li>
            <li class="menu-item"><a href="#" class="menu-link"><i class="ti ti-messages"></i><span>Chat</span></a></li>
            <li class="menu-item"><a href="#" class="menu-link"><i class="ti ti-calendar"></i><span>Calendar</span></a></li>
            <li class="menu-item has-submenu">
                <a href="javascript:void(0)" class="menu-link submenu-toggle"><i class="ti ti-file-invoice"></i><span>Invoice</span><i class="ti ti-chevron-right submenu-arrow"></i></a>
                <ul class="submenu"><li><a href="#">List</a></li><li><a href="#">Preview</a></li><li><a href="#">Edit</a></li><li><a href="#">Add</a></li></ul>
            </li>
            <li class="menu-item has-submenu">
                <a href="javascript:void(0)" class="menu-link submenu-toggle"><i class="ti ti-users"></i><span>Users</span><i class="ti ti-chevron-right submenu-arrow"></i></a>
                <ul class="submenu"><li><a href="#">List</a></li><li><a href="#">View</a></li></ul>
            </li>
            <li class="menu-item"><a href="#" class="menu-link"><i class="ti ti-lock"></i><span>Roles & Permissions</span></a></li>
            <li class="menu-header">Components</li>
            <li class="menu-item"><a href="#" class="menu-link"><i class="ti ti-text-size"></i><span>Typography</span></a></li>
            <li class="menu-item"><a href="#" class="menu-link"><i class="ti ti-square"></i><span>Icons</span></a></li>
            <li class="menu-item"><a href="#" class="menu-link"><i class="ti ti-cards"></i><span>Cards</span></a></li>
            <li class="menu-header">Forms & Tables</li>
            <li class="menu-item"><a href="#" class="menu-link"><i class="ti ti-toggle-left"></i><span>Form Elements</span></a></li>
            <li class="menu-item"><a href="#" class="menu-link"><i class="ti ti-layout-grid"></i><span>Form Layouts</span></a></li>
            <li class="menu-item"><a href="#" class="menu-link"><i class="ti ti-table"></i><span>Tables</span></a></li>
            <li class="menu-header">Charts & Misc</li>
            <li class="menu-item"><a href="#" class="menu-link"><i class="ti ti-chart-bar"></i><span>Charts</span></a></li>
            <li class="menu-item"><a href="#" class="menu-link"><i class="ti ti-box-multiple"></i><span>Misc</span></a></li>
        </ul> -->
    </aside>

    <!-- Navbar -->
    <nav class="layout-navbar" id="layoutNavbar">
        <button class="menu-toggle-btn" id="menuToggle" aria-label="Abrir barra lateral"><i class="ti ti-menu-2"></i></button>
        <div class="navbar-search" id="navbarSearchTrigger">
            <i class="ti ti-search"></i>
            <input type="text" placeholder="Search (Ctrl+/)" readonly>
        </div>
        <div class="navbar-end">
            {{-- ─── Notification Bell ──────────────────────────────── --}}
            @auth
            <div class="dropdown ethos-notif-dropdown" id="notifDropdown">
                <button class="navbar-icon-btn position-relative" id="notifBell"
                        data-bs-toggle="dropdown" data-bs-auto-close="outside"
                        aria-expanded="false" aria-label="Notificaciones">
                    <i class="ti ti-bell"></i>
                    <span class="ethos-notif-badge d-none" id="notifBadge">0</span>
                </button>
                <div class="dropdown-menu dropdown-menu-end ethos-notif-panel p-0" id="notifPanel">
                    <div class="ethos-notif-header d-flex justify-content-between align-items-center px-3 py-2">
                        <span class="fw-semibold">Notificaciones</span>
                        <button class="btn btn-sm btn-link p-0 text-muted" id="notifMarkAll" style="font-size:.8rem">Marcar todo como leído</button>
                    </div>
                    <div id="notifList" style="max-height:360px;overflow-y:auto;">
                        <div class="text-center text-muted py-4 small" id="notifEmpty">Cargando...</div>
                    </div>
                    <div class="ethos-notif-footer text-center py-2 border-top">
                        <a href="/admin/notifications" class="small text-primary text-decoration-none">Ver todas</a>
                    </div>
                </div>
            </div>
            @endauth

            <button class="navbar-icon-btn" id="themeSwitcher" title="Dark/Light Mode" aria-label="Alternar modo oscuro"><i class="ti ti-moon" id="themeIcon"></i></button>
                <div class="navbar-user dropdown">
                @php
                    $navUser = auth()->user();
                    $navInitials = $navUser?->initials ?? 'U';
                    $navRole = $navUser?->getRoleNames()->first() ?? 'Usuario';
                @endphp
                <a href="#" class="d-flex align-items-center gap-2 text-decoration-none" data-bs-toggle="dropdown" aria-expanded="false">
                    <div class="navbar-user-avatar" id="navAvatarTop">
                        @if($navUser?->avatar_url)
                            <img src="{{ $navUser->avatar_url }}" style="width:100%;height:100%;object-fit:cover;border-radius:inherit;">
                        @else
                            {{ $navInitials }}
                        @endif
                    </div>
                    <div class="navbar-user-info d-none d-md-flex">
                        <span class="navbar-user-name">{{ $navUser?->name ?? 'Usuario' }}</span>
                        <span class="navbar-user-role">{{ $navRole }}</span>
                    </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end" style="min-width:220px;">
                    <li class="px-3 py-2 d-flex align-items-center gap-2 border-bottom mb-1">
                        <div class="navbar-user-avatar" style="width:42px;height:42px;font-size:1rem;">
                            @if($navUser?->avatar_url)
                                <img src="{{ $navUser->avatar_url }}" style="width:100%;height:100%;object-fit:cover;border-radius:inherit;">
                            @else
                                {{ $navInitials }}
                            @endif
                        </div>
                        <div>
                            <div class="fw-semibold" style="color:var(--vz-heading-color)">{{ $navUser?->name ?? 'Usuario' }}</div>
                            <small class="text-muted">{{ $navUser?->email ?? '' }}</small>
                        </div>
                    </li>
                    <li><a class="dropdown-item" href="{{ route('profile.edit') }}"><i class="ti ti-user me-2"></i>Mi Perfil</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <a class="dropdown-item text-danger" href="{{ route('logout') }}" onclick="event.preventDefault(); this.closest('form').submit();">
                                <i class="ti ti-power me-2"></i>Cerrar Sesión
                            </a>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="layout-page" id="layoutPage">
        <div class="content-wrapper" id="mainContent">@yield('content')</div>
        <footer class="content-footer">
            <div class="d-flex justify-content-between align-items-center">
                <span>© {{ date('Y') }} <a href="/" class="text-primary text-decoration-none fw-semibold">ETHOS</a></span>
                <div class="d-flex gap-3">
                    <a href="#" class="text-muted text-decoration-none" style="font-size:0.8125rem">Licencia</a>
                    <a href="#" class="text-muted text-decoration-none" style="font-size:0.8125rem">Documentación</a>
                    <a href="#" class="text-muted text-decoration-none" style="font-size:0.8125rem">Soporte</a>
                </div>
            </div>
        </footer>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts@3.44.0/dist/apexcharts.min.js"></script>
    <script>
    (function(){
        const $ = s => document.querySelector(s);
        const $$ = s => document.querySelectorAll(s);
        const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

        // Sidebar toggle (mobile)
        $('#menuToggle')?.addEventListener('click', () => { $('#layoutMenu').classList.toggle('show'); $('#sidebarOverlay').classList.toggle('show'); });
        $('#sidebarOverlay')?.addEventListener('click', () => { $('#layoutMenu').classList.remove('show'); $('#sidebarOverlay').classList.remove('show'); });
        $('#sidebarClose')?.addEventListener('click', () => { $('#layoutMenu').classList.remove('show'); $('#sidebarOverlay').classList.remove('show'); });

        // Sidebar collapse (desktop)
        $('#sidebarCollapse')?.addEventListener('click', () => {
            document.documentElement.classList.toggle('layout-menu-collapsed');
            const icon = $('#sidebarCollapse i');
            icon.className = document.documentElement.classList.contains('layout-menu-collapsed') ? 'ti ti-circle' : 'ti ti-circle-dot';
        });

        // Submenus
        $$('.submenu-toggle').forEach(btn => {
            btn.addEventListener('click', () => { btn.closest('.has-submenu').classList.toggle('open'); });
        });

        // Dark mode
        $('#themeSwitcher')?.addEventListener('click', () => {
            const isDark = !document.documentElement.classList.contains('dark-style');
            if (isDark) {
                document.documentElement.classList.remove('light-style');
                document.documentElement.classList.add('dark-style');
            } else {
                document.documentElement.classList.remove('dark-style');
                document.documentElement.classList.add('light-style');
            }
            $('#themeIcon').className = isDark ? 'ti ti-sun' : 'ti ti-moon';
            localStorage.setItem('theme', isDark ? 'dark' : 'light');
        });
        
        // Sync icon on load
        if (document.documentElement.classList.contains('dark-style')) {
            if ($('#themeIcon')) $('#themeIcon').className = 'ti ti-sun';
        } else {
            if ($('#themeIcon')) $('#themeIcon').className = 'ti ti-moon';
        }

        // Search modal
        function openSearch(){ $('#searchModal').classList.add('show'); setTimeout(()=>$('#searchModalInput')?.focus(),100); }
        function closeSearch(){ $('#searchModal').classList.remove('show'); if($('#searchModalInput')){ $('#searchModalInput').value=''; $('#searchModalInput').setAttribute('aria-expanded','false'); } clearSearchUI(); }
        $('#navbarSearchTrigger')?.addEventListener('click', openSearch);
        $('#searchModal')?.addEventListener('click', e=>{ if(e.target===$('#searchModal')) closeSearch(); });
        document.addEventListener('keydown', e=>{
            if((e.ctrlKey||e.metaKey)&&e.key==='/'){ e.preventDefault(); openSearch(); }
            if(e.key==='Escape') closeSearch();
        });

        const HISTORY_KEY = 'ethos.search.history.v1';
        let activeCategory = 'all';
        let activeIndex = -1;
        let lastSearchAbort = null;
        let debounceTimer = null;

        function readHistory(){
            try {
                const raw = localStorage.getItem(HISTORY_KEY);
                const list = raw ? JSON.parse(raw) : [];
                return Array.isArray(list) ? list.filter(x => typeof x === 'string') : [];
            } catch(e){
                return [];
            }
        }

        function writeHistory(list){
            try { localStorage.setItem(HISTORY_KEY, JSON.stringify(list.slice(0, 8))); } catch(e) {}
        }

        function pushHistory(query){
            const q = String(query || '').trim();
            if(!q) return;
            const prev = readHistory();
            const next = [q, ...prev.filter(x => x.toLowerCase() !== q.toLowerCase())];
            writeHistory(next);
        }

        function clearSearchUI(){
            activeIndex = -1;
            $('#searchStatus') && ($('#searchStatus').textContent='');
            $('#searchSuggestionsSection') && ($('#searchSuggestionsSection').style.display='none');
            $('#searchSuggestionsList') && ($('#searchSuggestionsList').innerHTML='');
            renderRecent();
        }

        function renderRecent(){
            const list = readHistory();
            const section = $('#searchRecentSection');
            const wrap = $('#searchRecentList');
            if(!section || !wrap) return;
            if(list.length === 0){
                section.style.display = 'none';
                wrap.innerHTML = '';
                return;
            }
            section.style.display = '';
            wrap.innerHTML = list.map(q => `
                <button type="button" class="search-result-item" data-recent="1" data-q="${encodeURIComponent(q)}" style="width:100%; text-align:left;">
                    <i class="ti ti-history"></i><span>${escapeHtml(q)}</span>
                </button>
            `).join('');
        }

        function escapeHtml(value){
            return String(value ?? '')
                .replaceAll('\x26','\x26amp;')
                .replaceAll('\x3c','\x26lt;')
                .replaceAll('\x3e','\x26gt;')
                .replaceAll('\x22','\x26quot;')
                .replaceAll("\x27",'\x26#039;');
        }

        function fmtTime(iso){
            if(!iso) return '';
            try {
                const d = new Date(iso);
                const now = new Date();
                const sameDay = d.toDateString() === now.toDateString();
                const time = d.toLocaleTimeString([], {hour:'2-digit', minute:'2-digit'});
                return sameDay ? `Hoy ${time}` : `${d.toLocaleDateString()} ${time}`;
            } catch(e){
                return '';
            }
        }

        async function apiFetch(url, options={}){
            const res = await fetch(url, {
                credentials: 'same-origin',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    ...(csrf ? {'X-CSRF-TOKEN': csrf} : {}),
                    ...(options.headers || {})
                },
                ...options,
            });
            return res;
        }

        function setSearchStatus(text){
            const el = $('#searchStatus');
            if(!el) return;
            el.textContent = text || '';
        }

        function setSearchResults(items){
            const section = $('#searchSuggestionsSection');
            const list = $('#searchSuggestionsList');
            if(!section || !list) return;
            if(!items || items.length === 0){
                section.style.display = 'none';
                list.innerHTML = '';
                return;
            }
            section.style.display = '';
            list.innerHTML = items.map((it, idx) => {
                const icon = it.icon || 'ti ti-search';
                const subtitle = it.subtitle ? `<div class="search-result-meta">${escapeHtml(it.subtitle)}</div>` : '';
                return `
                    <a href="${escapeHtml(it.url || '#')}" class="search-result-item" role="option" aria-selected="false" data-idx="${idx}">
                        <i class="${escapeHtml(icon)}"></i>
                        <span>${escapeHtml(it.title || '')}</span>
                        ${subtitle}
                    </a>
                `;
            }).join('');
            $('#searchModalInput')?.setAttribute('aria-expanded','true');
        }

        async function runSearch(query){
            const q = String(query || '').trim();
            if(q.length === 0){
                clearSearchUI();
                return;
            }
            if(q.length < 2){
                setSearchStatus('Escribe al menos 2 caracteres.');
                setSearchResults([]);
                $('#searchSuggestionsSection') && ($('#searchSuggestionsSection').style.display='none');
                return;
            }

            setSearchStatus('Buscando...');

            if(lastSearchAbort){
                lastSearchAbort.abort();
            }
            lastSearchAbort = new AbortController();

            const params = new URLSearchParams({q: q, category: activeCategory});
            try {
                const res = await apiFetch(`/admin/search?${params.toString()}`, { signal: lastSearchAbort.signal });
                if(!res.ok){
                    setSearchStatus('No se pudo buscar.');
                    setSearchResults([]);
                    return;
                }
                const data = await res.json();
                setSearchStatus(data.items && data.items.length ? '' : 'Sin resultados.');
                setSearchResults(data.items || []);
            } catch(e){
                if(e && e.name === 'AbortError') return;
                setSearchStatus('Error de red al buscar.');
                setSearchResults([]);
            }
        }

        $$('.search-filter-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                $$('.search-filter-btn').forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                activeCategory = btn.dataset.category || 'all';
                runSearch($('#searchModalInput')?.value || '');
            });
        });

        $('#searchClearHistoryBtn')?.addEventListener('click', () => {
            writeHistory([]);
            renderRecent();
        });

        $('#searchResults')?.addEventListener('click', (e) => {
            const target = e.target.closest('[data-recent="1"]');
            if(!target) return;
            const q = decodeURIComponent(target.getAttribute('data-q') || '');
            if($('#searchModalInput')){
                $('#searchModalInput').value = q;
                $('#searchModalInput').focus();
            }
            runSearch(q);
        });

        $('#searchSuggestionsList')?.addEventListener('click', (e) => {
            const a = e.target.closest('a.search-result-item');
            if(!a) return;
            const text = $('#searchModalInput')?.value || '';
            pushHistory(text);
        });

        $('#searchModalInput')?.addEventListener('input', function(){
            const q = this.value;
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => runSearch(q), 160);
        });

        $('#searchModalInput')?.addEventListener('keydown', function(e){
            const list = $('#searchSuggestionsList');
            if(!list) return;
            const items = Array.from(list.querySelectorAll('a.search-result-item'));
            if(items.length === 0) return;
            if(e.key === 'ArrowDown'){
                e.preventDefault();
                activeIndex = Math.min(items.length - 1, activeIndex + 1);
            } else if(e.key === 'ArrowUp'){
                e.preventDefault();
                activeIndex = Math.max(0, activeIndex - 1);
            } else if(e.key === 'Enter'){
                if(activeIndex >= 0 && items[activeIndex]){
                    e.preventDefault();
                    const text = $('#searchModalInput')?.value || '';
                    pushHistory(text);
                    window.location.href = items[activeIndex].getAttribute('href');
                }
                return;
            } else {
                return;
            }

            items.forEach((el, idx) => {
                el.setAttribute('aria-selected', idx === activeIndex ? 'true' : 'false');
                el.classList.toggle('active', idx === activeIndex);
            });
            items[activeIndex]?.scrollIntoView({block:'nearest'});
        });

        $('#navbarSearchTrigger')?.addEventListener('click', () => {
            renderRecent();
            setSearchStatus('');
        });

        // Active menu highlighting
        $$('.menu-link:not(.submenu-toggle)').forEach(link=>{
            if(link.getAttribute('href')===window.location.pathname) link.classList.add('active');
            link.addEventListener('click', ()=>{ $$('.menu-link').forEach(l=>l.classList.remove('active')); link.classList.add('active'); });
        });

        // Tooltip init
        $$('[title]').forEach(el=>{ if(el.classList.contains('navbar-icon-btn')) new bootstrap.Tooltip(el,{placement:'bottom',trigger:'hover'}); });
    })();
    </script>

    {{-- ─── Notification Bell JS ────────────────────────────────────── --}}
    @auth
    <script>
    (function () {
        const bell    = document.getElementById('notifBell');
        const badge   = document.getElementById('notifBadge');
        const list    = document.getElementById('notifList');
        const markAll = document.getElementById('notifMarkAll');
        if (!bell) return;

        const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';
        let loaded = false;

        function updateBadge(count) {
            if (count > 0) {
                badge.textContent = count > 99 ? '99+' : count;
                badge.classList.remove('d-none');
            } else {
                badge.classList.add('d-none');
            }
        }

        function levelIcon(level) {
            const map = { warning: 'ti-alert-triangle text-warning', info: 'ti-info-circle text-info', success: 'ti-circle-check text-success', error: 'ti-circle-x text-danger' };
            return map[level] || 'ti-bell text-secondary';
        }

        function renderItems(items) {
            if (!items.length) {
                list.innerHTML = '<div class="text-center text-muted py-4 small">No tienes notificaciones.</div>';
                return;
            }
            list.innerHTML = items.map(n => `
                <a class="ethos-notif-item ${n.read ? '' : 'unread'}" href="${n.action_url || '#'}"
                   data-notif-id="${n.id}" data-read="${n.read ? '1' : '0'}">
                    <span class="ethos-notif-dot ${n.read ? 'read' : ''}"></span>
                    <div class="flex-grow-1 min-w-0">
                        <div class="ethos-notif-title">${escHtml(n.title)}</div>
                        <div class="ethos-notif-msg">${escHtml(n.message)}</div>
                    </div>
                    <span class="ethos-notif-time">${escHtml(n.created_at)}</span>
                </a>
            `).join('');

            // Mark as read on click
            list.querySelectorAll('.ethos-notif-item').forEach(el => {
                el.addEventListener('click', async function (e) {
                    const id   = this.dataset.notifId;
                    const read = this.dataset.read === '1';
                    if (!read) {
                        await fetch(`/admin/notifications/${id}/read`, {
                            method: 'PATCH',
                            headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
                        }).catch(() => {});
                        this.classList.remove('unread');
                        this.dataset.read = '1';
                        this.querySelector('.ethos-notif-dot')?.classList.add('read');
                        const current = parseInt(badge.textContent) || 0;
                        updateBadge(Math.max(0, current - 1));
                    }
                });
            });
        }

        function escHtml(str) {
            return String(str ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
        }

        async function loadNotifications() {
            try {
                const res  = await fetch('/admin/notifications', { headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf } });
                const data = await res.json();
                updateBadge(data.unread_count ?? 0);
                renderItems(data.items ?? []);
                loaded = true;
            } catch (e) {
                list.innerHTML = '<div class="text-center text-muted py-3 small">Error al cargar notificaciones.</div>';
            }
        }

        // Load on first open
        document.getElementById('notifDropdown')?.addEventListener('show.bs.dropdown', () => {
            if (!loaded) loadNotifications();
        });

        // Mark all read
        markAll?.addEventListener('click', async (e) => {
            e.stopPropagation();
            await fetch('/admin/notifications/mark-all-read', {
                method: 'PATCH',
                headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
            }).catch(() => {});
            updateBadge(0);
            list.querySelectorAll('.ethos-notif-item.unread').forEach(el => {
                el.classList.remove('unread');
                el.dataset.read = '1';
                el.querySelector('.ethos-notif-dot')?.classList.add('read');
            });
        });

        // Poll unread count every 60 s
        async function pollCount() {
            try {
                const res  = await fetch('/admin/notifications?filter=unread&limit=1', { headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf } });
                const data = await res.json();
                updateBadge(data.unread_count ?? 0);
                if (data.unread_count > 0 && loaded) {
                    loaded = false; // force reload on next open
                }
            } catch {}
        }
        pollCount();
        setInterval(pollCount, 60000);
    })();
    </script>
    @endauth

    @vite(['resources/js/app.js'])
    @stack('scripts')
    @auth
        @if(auth()->user()?->can('admin.access'))
            @include('admin.chatbot')
        @endif
    @endauth
</body>
</html>
