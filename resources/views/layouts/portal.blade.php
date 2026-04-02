<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Portal Cliente — ETHOS &mdash; @yield('title', 'Mi Proyecto')</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@2.47.0/tabler-icons.min.css">
    <style>
        :root {
            --ethos-primary: #1a3c5e;
            --ethos-accent:  #2980b9;
        }
        body { font-family: 'Inter', sans-serif; background: #f5f7fa; color: #2c3e50; }
        .portal-header {
            background: var(--ethos-primary);
            color: #fff;
            padding: 14px 0;
        }
        .portal-header .brand { font-weight: 700; font-size: 1.25rem; letter-spacing: 1px; }
        .portal-header .subtitle { font-size: .8rem; opacity: .7; }
        .portal-main { padding: 32px 0 64px; }
        .card { border: none; border-radius: 12px; box-shadow: 0 2px 12px rgba(0,0,0,.06); }
        .card-header { background: transparent; border-bottom: 1px solid #eee; font-weight: 600; }
        .badge-status { font-size: .72rem; padding: 4px 10px; border-radius: 20px; }
        .timeline-dot { width: 10px; height: 10px; border-radius: 50%; display: inline-block; margin-right: 6px; }
        footer { background: var(--ethos-primary); color: rgba(255,255,255,.6); font-size: .8rem; padding: 16px 0; text-align: center; }
    </style>
    @stack('styles')
</head>
<body>

<header class="portal-header">
    <div class="container d-flex align-items-center justify-content-between">
        <div>
            <div class="brand">ETHOS</div>
            <div class="subtitle">Portal de Seguimiento de Proyectos</div>
        </div>
        <div class="text-white-50 small">
            <i class="ti ti-lock me-1"></i>Acceso privado
        </div>
    </div>
</header>

<main class="portal-main">
    <div class="container">
        @yield('content')
    </div>
</main>

<footer>
    <div class="container">
        &copy; {{ date('Y') }} ETHOS Consultoría &mdash; Todos los derechos reservados.
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.1/dist/cdn.min.js"></script>
@stack('scripts')
</body>
</html>
