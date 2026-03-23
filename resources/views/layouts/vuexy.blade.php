<!DOCTYPE html>
<html lang="es" class="light-style layout-menu-fixed layout-navbar-fixed" dir="ltr" data-theme="theme-default">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') - ETHOS Admin</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@2.44.0/tabler-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/apexcharts@3.44.0/dist/apexcharts.css">
    <link rel="stylesheet" href="{{ asset('css/vuexy.css') }}">
    @stack('styles')
</head>
<body>
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Search Modal -->
    <div class="search-modal" id="searchModal">
        <div class="search-modal-content">
            <div class="search-modal-header">
                <i class="ti ti-search"></i>
                <input type="text" id="searchModalInput" placeholder="Buscar navegación, páginas...">
                <kbd>ESC</kbd>
            </div>
            <div class="search-modal-body" id="searchResults">
                <div class="search-section-title">Popular Searches</div>
                <a href="/admin/dashboard" class="search-result-item"><i class="ti ti-smart-home"></i><span>inicio</span></a>
                <a href="#" class="search-result-item"><i class="ti ti-file-invoice"></i><span>Invoice List</span></a>
                <a href="#" class="search-result-item"><i class="ti ti-users"></i><span>User List</span></a>
                <a href="#" class="search-result-item"><i class="ti ti-settings"></i><span>Account Settings</span></a>
            </div>
        </div>
    </div>

    <!-- Notifications Panel -->
    <div class="notifications-panel" id="notificationsPanel">
        <div class="notif-header">
            <h6>Notificaciones</h6>
            <span class="badge-label bg-label-primary">8 nuevas</span>
        </div>
        <div class="notif-tabs">
            <button class="notif-tab active" data-tab="all">Todo</button>
            <button class="notif-tab" data-tab="unread">Sin leer</button>
            <button class="notif-tab" data-tab="archived">Archivadas</button>
        </div>
        <div class="notif-list">
            <div class="notif-item unread">
                <div class="notif-avatar bg-label-primary"><i class="ti ti-award"></i></div>
                <div class="notif-info">
                    <div class="notif-title">¡Felicidades! 🎉</div>
                    <div class="notif-desc">Has ganado la medalla de oro por rendimiento excepcional.</div>
                    <div class="notif-time">Hoy</div>
                </div>
            </div>
            <div class="notif-item unread">
                <div class="notif-avatar bg-label-danger"><i class="ti ti-message-dots"></i></div>
                <div class="notif-info">
                    <div class="notif-title">Nuevo mensaje recibido</div>
                    <div class="notif-desc">Tienes 10 mensajes sin leer de María R.</div>
                    <div class="notif-time">Hace 1 hora</div>
                </div>
            </div>
            <div class="notif-item">
                <div class="notif-avatar bg-label-success"><i class="ti ti-shopping-cart"></i></div>
                <div class="notif-info">
                    <div class="notif-title">Nuevo pedido generado</div>
                    <div class="notif-desc">Consultoría #5090 por valor de $3,200.</div>
                    <div class="notif-time">Hace 2 horas</div>
                </div>
            </div>
            <div class="notif-item">
                <div class="notif-avatar bg-label-warning"><i class="ti ti-alert-triangle"></i></div>
                <div class="notif-info">
                    <div class="notif-title">Factura vencida</div>
                    <div class="notif-desc">La factura #5086 ha excedido el plazo de pago.</div>
                    <div class="notif-time">Ayer</div>
                </div>
            </div>
        </div>
        <div class="notif-footer">
            <button class="btn-read-all">Marcar todas como leídas</button>
        </div>
    </div>

    <!-- Shortcuts Panel -->
    <div class="shortcuts-panel" id="shortcutsPanel">
        <div class="notif-header"><h6>Accesos Rápidos</h6></div>
        <div class="shortcuts-grid">
            <a href="#" class="shortcut-item"><div class="shortcut-icon bg-label-primary"><i class="ti ti-calendar-event"></i></div><span>Calendario</span></a>
            <a href="#" class="shortcut-item"><div class="shortcut-icon bg-label-success"><i class="ti ti-file-invoice"></i></div><span>Facturación</span></a>
            <a href="#" class="shortcut-item"><div class="shortcut-icon bg-label-danger"><i class="ti ti-users"></i></div><span>Usuarios</span></a>
            <a href="#" class="shortcut-item"><div class="shortcut-icon bg-label-warning"><i class="ti ti-lock"></i></div><span>Roles</span></a>
            <a href="/admin/dashboard" class="shortcut-item"><div class="shortcut-icon bg-label-info"><i class="ti ti-chart-bar"></i></div><span>Dashboard</span></a>
            <a href="#" class="shortcut-item"><div class="shortcut-icon bg-label-primary"><i class="ti ti-settings"></i></div><span>Ajustes</span></a>
        </div>
    </div>

    <!-- Sidebar -->
    <aside class="layout-menu" id="layoutMenu">
        <div class="app-brand">
            <a href="/admin/dashboard" class="app-brand-link">
                <span class="brand-text">ETHOS</span>
            </a>
            <button class="sidebar-toggler d-none d-xl-block" id="sidebarCollapse" title="Toggle sidebar">
                <i class="ti ti-circle-dot"></i>
            </button>
            <button class="sidebar-close d-xl-none" id="sidebarClose"><i class="ti ti-x"></i></button>
        </div>
        <ul class="menu-inner" style="list-style:none;padding:0;">
            <li class="menu-header">Dashboards <span class="menu-badge badge-label bg-label-danger">5</span></li>
            <li class="menu-item"><a href="/admin/dashboard" class="menu-link {{ request()->is('admin/dashboard') ? 'active' : '' }}"><i class="ti ti-smart-home"></i><span>Inicio</span></a></li>
            @can('clients.view')
            <li class="menu-item"><a href="/admin/clients" class="menu-link {{ request()->is('admin/clients*') ? 'active' : '' }}"><i class="ti ti-users"></i><span>Clientes</span></a></li>
            @endcan
            @can('projects.view')
            <li class="menu-item"><a href="/admin/projects" class="menu-link {{ request()->is('admin/projects*') ? 'active' : '' }}"><i class="ti ti-briefcase"></i><span>Proyectos</span></a></li>
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
        <button class="menu-toggle-btn" id="menuToggle"><i class="ti ti-menu-2"></i></button>
        <div class="navbar-search" id="navbarSearchTrigger">
            <i class="ti ti-search"></i>
            <input type="text" placeholder="Search (Ctrl+/)" readonly>
        </div>
        <div class="navbar-end">
            <button class="navbar-icon-btn" id="langSwitcher" title="Language"><i class="ti ti-language"></i></button>
            <button class="navbar-icon-btn" id="themeSwitcher" title="Dark/Light Mode"><i class="ti ti-moon" id="themeIcon"></i></button>
            <button class="navbar-icon-btn" id="shortcutsBtn" title="Shortcuts"><i class="ti ti-layout-grid-add"></i></button>
            <button class="navbar-icon-btn" id="notifBtn" title="Notifications"><i class="ti ti-bell"></i><span class="badge-dot"></span></button>
            <div class="navbar-user dropdown">
                <a href="#" class="d-flex align-items-center gap-2 text-decoration-none" data-bs-toggle="dropdown" aria-expanded="false">
                    <div class="navbar-user-avatar">AD</div>
                    <div class="navbar-user-info d-none d-md-flex">
                        <span class="navbar-user-name">Admin</span>
                        <span class="navbar-user-role">Administrador</span>
                    </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end" style="min-width:220px;">
                    <li class="px-3 py-2 d-flex align-items-center gap-2 border-bottom mb-1">
                        <div class="navbar-user-avatar" style="width:42px;height:42px;font-size:1rem;">AD</div>
                        <div><div class="fw-semibold" style="color:var(--vz-heading-color)">Admin</div><small class="text-muted">admin@ethos.com</small></div>
                    </li>
                    <li><a class="dropdown-item" href="#"><i class="ti ti-user me-2"></i>Mi Perfil<span class="badge-label bg-label-danger ms-auto">4</span></a></li>
                    <li><a class="dropdown-item" href="#"><i class="ti ti-settings me-2"></i>Configuración</a></li>
                    <li><a class="dropdown-item" href="#"><i class="ti ti-currency-dollar me-2"></i>Facturación</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="#"><i class="ti ti-lifebuoy me-2"></i>Ayuda</a></li>
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
        <div class="content-wrapper">@yield('content')</div>
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
            document.documentElement.classList.toggle('dark-style');
            document.documentElement.classList.toggle('light-style');
            const isDark = document.documentElement.classList.contains('dark-style');
            $('#themeIcon').className = isDark ? 'ti ti-sun' : 'ti ti-moon';
            localStorage.setItem('theme', isDark ? 'dark' : 'light');
        });
        if(localStorage.getItem('theme')==='dark'){
            document.documentElement.classList.add('dark-style');
            document.documentElement.classList.remove('light-style');
            if($('#themeIcon')) $('#themeIcon').className='ti ti-sun';
        }

        // Search modal
        function openSearch(){ $('#searchModal').classList.add('show'); setTimeout(()=>$('#searchModalInput')?.focus(),100); }
        function closeSearch(){ $('#searchModal').classList.remove('show'); $('#searchModalInput').value=''; }
        $('#navbarSearchTrigger')?.addEventListener('click', openSearch);
        $('#searchModal')?.addEventListener('click', e=>{ if(e.target===$('#searchModal')) closeSearch(); });
        document.addEventListener('keydown', e=>{
            if((e.ctrlKey||e.metaKey)&&e.key==='/'){ e.preventDefault(); openSearch(); }
            if(e.key==='Escape') closeSearch();
        });
        $('#searchModalInput')?.addEventListener('input', function(){
            const q = this.value.toLowerCase();
            $$('.search-result-item').forEach(item=>{ item.style.display = item.textContent.toLowerCase().includes(q)||!q ? '' : 'none'; });
        });

        // Panels (notifications, shortcuts)
        function togglePanel(panel){ const p=$(panel); const isOpen=p.classList.contains('show'); $$('.notifications-panel,.shortcuts-panel').forEach(x=>x.classList.remove('show')); if(!isOpen) p.classList.add('show'); }
        $('#notifBtn')?.addEventListener('click', e=>{ e.stopPropagation(); togglePanel('#notificationsPanel'); });
        $('#shortcutsBtn')?.addEventListener('click', e=>{ e.stopPropagation(); togglePanel('#shortcutsPanel'); });
        document.addEventListener('click', e=>{ if(!e.target.closest('.notifications-panel,.shortcuts-panel,.navbar-icon-btn')) $$('.notifications-panel,.shortcuts-panel').forEach(x=>x.classList.remove('show')); });

        // Notification tabs
        $$('.notif-tab').forEach(tab=>{ tab.addEventListener('click', ()=>{ $$('.notif-tab').forEach(t=>t.classList.remove('active')); tab.classList.add('active'); }); });
        $('.btn-read-all')?.addEventListener('click', ()=>{ $$('.notif-item.unread').forEach(n=>n.classList.remove('unread')); });

        // Active menu highlighting
        $$('.menu-link:not(.submenu-toggle)').forEach(link=>{
            if(link.getAttribute('href')===window.location.pathname) link.classList.add('active');
            link.addEventListener('click', ()=>{ $$('.menu-link').forEach(l=>l.classList.remove('active')); link.classList.add('active'); });
        });

        // Tooltip init
        $$('[title]').forEach(el=>{ if(el.classList.contains('navbar-icon-btn')) new bootstrap.Tooltip(el,{placement:'bottom',trigger:'hover'}); });
    })();
    </script>
    @stack('scripts')
</body>
</html>
