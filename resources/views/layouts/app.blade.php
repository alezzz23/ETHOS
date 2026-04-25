<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title', config('app.name', 'Laravel'))</title>
        <meta name="description" content="@yield('meta_description', 'Consultoría estratégica, auditoría y procesos empresariales en Caracas. ETHOS.')">
        <meta name="keywords" content="@yield('meta_keywords', 'consultoría estratégica, auditoría, procesos, Caracas, Venezuela, ETHOS')">
        <meta name="author" content="ETHOS Summit Group">
        <link rel="canonical" href="@yield('canonical', url()->current())">

        <!-- Open Graph / Facebook -->
        <meta property="og:type" content="@yield('og_type', 'website')">
        <meta property="og:url" content="@yield('og_url', url()->current())">
        <meta property="og:title" content="@yield('og_title', 'ETHOS | Consultoría Estratégica Empresarial')">
        <meta property="og:description" content="@yield('og_description', 'Consultoría estratégica y auditoría para empresas en Caracas.')">
        <meta property="og:image" content="@yield('og_image', asset('images/ethos-og.jpg'))">
        <meta property="og:site_name" content="ETHOS Consultoría">
        <meta property="og:locale" content="es_VE">

        <!-- Twitter -->
        <meta property="twitter:card" content="summary_large_image">
        <meta property="twitter:url" content="@yield('twitter_url', url()->current())">
        <meta property="twitter:title" content="@yield('twitter_title', 'ETHOS | Consultoría Estratégica Empresarial')">
        <meta property="twitter:description" content="@yield('twitter_description', 'Consultoría estratégica y auditoría para empresas en Caracas.')">

        <!-- Schema.org Markup (opcional, por vista) -->
        @yield('structured_data')

        <!-- Favicon -->
        <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @if (isset($header))
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>
    </body>
</html>
