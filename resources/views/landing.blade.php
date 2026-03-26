<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Primary Meta Tags -->
    <title>ETHOS | Consultoría Estratégica Empresarial, Auditoría y Procesos en Caracas</title>
    <meta name="title" content="ETHOS | Consultoría Estratégica Empresarial y Auditoría">
    <meta name="description" content="Diseñamos procesos corporativos inteligentes, auditoría estratégica interna y sistemas empresariales sólidos para el crecimiento. Basados en Caracas, Venezuela.">
    <meta name="keywords" content="consultoría estratégica, auditoría estratégica, auditoría interna, procesos corporativos, estructuración organizacional, sistemas empresariales, Caracas, Venezuela, ETHOS">
    <meta name="author" content="ETHOS Summit Group">
    
    <!-- Canonical URL -->
    <link rel="canonical" href="{{ url()->current() }}">
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="ETHOS | Consultoría Estratégica Empresarial">
    <meta property="og:description" content="Diseñamos procesos inteligentes y auditoría estratégica para escalar y proteger tu empresa. Convertimos retos en resultados.">
    <meta property="og:image" content="{{ asset('images/ethos-og.jpg') }}">
    <meta property="og:site_name" content="ETHOS Consultoría">
    <meta property="og:locale" content="es_VE">
    
    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ url()->current() }}">
    <meta property="twitter:title" content="ETHOS | Consultoría Estratégica Empresarial">
    <meta property="twitter:description" content="Diseñamos procesos inteligentes y auditoría estratégica para escalar y proteger tu empresa.">
    
    <!-- Geo / Local SEO -->
    <meta name="geo.region" content="VE-A">
    <meta name="geo.placename" content="Caracas">
    
    <!-- Schema.org Markup -->
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "ConsultingBusiness",
      "name": "ETHOS Summit Group",
      "description": "Firma de consultoría estratégica y auditoría empresarial especializada en diseño de procesos inteligentes y estructuración organizacional.",
      "url": "{{ url('/') }}",
      "email": "ethossummitgroup@gmail.com",
      "address": {
        "@type": "PostalAddress",
        "addressLocality": "Caracas",
        "addressCountry": "VE"
      },
      "sameAs": [
        "https://www.instagram.com/ethosconsultoria_"
      ]
    }
    </script>
 
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,500;0,600;0,700;1,400&family=Cinzel:wght@400;500;600;700;800;900&family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
 
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'ethos': {
                            50: '#f0f7ff',
                            100: '#e0efff',
                            200: '#b9d9ff',
                            300: '#7cc0ff',
                            400: '#36a3ff',
                            500: '#0c88f0',
                            600: '#0066cc',
                            700: '#0052a3',
                            800: '#054380',
                            900: '#0a3a5c',
                            950: '#071f33',
                        },
                        'marble': {
                            50: '#fdfcfa',
                            100: '#f8f6f3',
                            200: '#efebe6',
                            300: '#e0d9d1',
                        },
                        'bronze': {
                            400: '#cd7f32',
                            500: '#b87333',
                            600: '#a0522d',
                        }
                    },
                    fontFamily: {
                        'display': ['Cinzel', 'serif'],
                        'serif': ['Cormorant Garamond', 'serif'],
                        'body': ['Outfit', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <style>
        :root {
            --ethos-primary: #0066cc;
            --ethos-secondary: #0a3a5c;
            --ethos-accent: #36a3ff;
            --transition-smooth: cubic-bezier(0.4, 0, 0.2, 1);
            --transition-bounce: cubic-bezier(0.68, -0.55, 0.265, 1.55);
            --transition-expo: cubic-bezier(0.16, 1, 0.3, 1);
        }
 
        * { box-sizing: border-box; }
        html { scroll-behavior: smooth; }
 
        /* Custom Cursor */
        .custom-cursor {
            width: 12px;
            height: 12px;
            border: 1.5px solid rgba(255, 255, 255, 0.6);
            border-radius: 50%;
            position: fixed;
            pointer-events: none;
            z-index: 9999;
            transition: transform 0.2s var(--transition-smooth), 
                        background 0.2s var(--transition-smooth),
                        border-color 0.2s var(--transition-smooth),
                        opacity 0.2s var(--transition-smooth);
            mix-blend-mode: difference;
            opacity: 0.7;
        }
 
        .custom-cursor.hover {
            transform: scale(2.5);
            background: rgba(255, 255, 255, 0.08);
            border-color: rgba(255, 255, 255, 0.9);
            opacity: 1;
        }
 
        /* Noise Texture Overlay */
        .noise-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 9998;
            opacity: 0.03;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noise'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noise)'/%3E%3C/svg%3E");
        }
 
        /* Subtle Dot Grid Pattern */
        .meander-complex {
            background-image: radial-gradient(circle, rgba(0, 102, 204, 0.06) 1px, transparent 1px);
            background-size: 32px 32px;
        }
 
        /* Animated Meander Border */
        .meander-border-animated {
            position: relative;
            overflow: hidden;
        }
 
        .meander-border-animated::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, 
                transparent 0%,
                #0066cc 10%,
                #36a3ff 30%,
                #0066cc 50%,
                #36a3ff 70%,
                #0066cc 90%,
                transparent 100%
            );
            background-size: 200% 100%;
            animation: meanderFlow 3s linear infinite;
        }
 
        @keyframes meanderFlow {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }
 
        /* Greek Column Decoration */
        .greek-column-decor {
            position: relative;
        }
 
        .greek-column-decor::before,
        .greek-column-decor::after {
            content: '';
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
            background: linear-gradient(180deg, 
                #f8f6f3 0%, 
                #efebe6 20%, 
                #f8f6f3 40%,
                #efebe6 60%,
                #f8f6f3 80%,
                #efebe6 100%
            );
            border-radius: 3px;
            box-shadow: inset 0 2px 4px rgba(0,0,0,0.1);
        }
 
        .greek-column-decor::before {
            top: -16px;
            width: 110%;
            height: 16px;
            clip-path: polygon(5% 100%, 0% 0%, 100% 0%, 95% 100%);
        }
 
        .greek-column-decor::after {
            bottom: -16px;
            width: 110%;
            height: 16px;
            clip-path: polygon(0% 100%, 5% 0%, 95% 0%, 100% 100%);
        }
 
        /* Parallax Container */
        .parallax-container {
            perspective: 1000px;
            transform-style: preserve-3d;
        }
 
        /* Hero Animations */
        @keyframes heroReveal {
            0% { 
                opacity: 0; 
                transform: translateY(80px) scale(0.95);
                filter: blur(10px);
            }
            100% { 
                opacity: 1; 
                transform: translateY(0) scale(1);
                filter: blur(0);
            }
        }
 
        @keyframes fadeInScale {
            0% { opacity: 0; transform: scale(0.8); }
            100% { opacity: 1; transform: scale(1); }
        }
 
        @keyframes slideInFromLeft {
            0% { opacity: 0; transform: translateX(-100px) rotate(-5deg); }
            100% { opacity: 1; transform: translateX(0) rotate(0); }
        }
 
        @keyframes slideInFromRight {
            0% { opacity: 0; transform: translateX(100px) rotate(5deg); }
            100% { opacity: 1; transform: translateX(0) rotate(0); }
        }
 
        @keyframes floatUp {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            25% { transform: translateY(-20px) rotate(1deg); }
            75% { transform: translateY(-10px) rotate(-1deg); }
        }
 
        @keyframes pulseGlow {
            0%, 100% { 
                box-shadow: 0 0 20px rgba(0, 102, 204, 0.3),
                            0 0 40px rgba(0, 102, 204, 0.1);
            }
            50% { 
                box-shadow: 0 0 40px rgba(0, 102, 204, 0.5),
                            0 0 80px rgba(0, 102, 204, 0.2);
            }
        }
 
        @keyframes shimmerText {
            0% { background-position: -200% center; }
            100% { background-position: 200% center; }
        }
 
        @keyframes rotateBorder {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
 
        @keyframes morphShape {
            0%, 100% { border-radius: 60% 40% 30% 70% / 60% 30% 70% 40%; }
            25% { border-radius: 30% 60% 70% 40% / 50% 60% 30% 60%; }
            50% { border-radius: 50% 60% 30% 60% / 30% 60% 70% 40%; }
            75% { border-radius: 60% 40% 60% 30% / 70% 30% 50% 60%; }
        }
 
        .animate-hero-reveal {
            animation: heroReveal 1.2s var(--transition-expo) forwards;
            opacity: 0;
        }
 
        .animate-fade-scale {
            animation: fadeInScale 0.8s var(--transition-expo) forwards;
            opacity: 0;
        }
 
        .animate-slide-left {
            animation: slideInFromLeft 1s var(--transition-expo) forwards;
            opacity: 0;
        }
 
        .animate-slide-right {
            animation: slideInFromRight 1s var(--transition-expo) forwards;
            opacity: 0;
        }
 
        .animate-float {
            animation: floatUp 8s ease-in-out infinite;
        }
 
        .animate-pulse-glow {
            animation: pulseGlow 3s ease-in-out infinite;
        }
 
        .animate-shimmer {
            background: linear-gradient(90deg, 
                transparent 0%, 
                rgba(255,255,255,0.5) 50%, 
                transparent 100%
            );
            background-size: 200% 100%;
            animation: shimmerText 2s infinite;
        }
 
        .animate-morph {
            animation: morphShape 15s ease-in-out infinite;
        }
 
        /* Delay utilities */
        .delay-100 { animation-delay: 0.1s; }
        .delay-200 { animation-delay: 0.2s; }
        .delay-300 { animation-delay: 0.3s; }
        .delay-400 { animation-delay: 0.4s; }
        .delay-500 { animation-delay: 0.5s; }
        .delay-600 { animation-delay: 0.6s; }
        .delay-700 { animation-delay: 0.7s; }
        .delay-800 { animation-delay: 0.8s; }
        .delay-1000 { animation-delay: 1s; }
        .delay-1200 { animation-delay: 1.2s; }
 
        /* Glass Effects */
        .glass-premium {
            background: linear-gradient(135deg, 
                rgba(255, 255, 255, 0.9) 0%, 
                rgba(255, 255, 255, 0.7) 50%,
                rgba(255, 255, 255, 0.8) 100%
            );
            backdrop-filter: blur(20px) saturate(180%);
            border: 1px solid rgba(255, 255, 255, 0.5);
            box-shadow: 
                0 8px 32px rgba(0, 102, 204, 0.1),
                inset 0 1px 0 rgba(255, 255, 255, 0.8),
                inset 0 -1px 0 rgba(0, 0, 0, 0.05);
        }
 
        .glass-dark-premium {
            background: linear-gradient(135deg, 
                rgba(7, 31, 51, 0.95) 0%, 
                rgba(10, 58, 92, 0.9) 50%,
                rgba(5, 67, 128, 0.85) 100%
            );
            backdrop-filter: blur(20px) saturate(180%);
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 
                0 8px 32px rgba(0, 0, 0, 0.3),
                inset 0 1px 0 rgba(255, 255, 255, 0.1);
        }
 
        /* Text Effects */
        .text-gradient-animated {
            background: linear-gradient(90deg, 
                #ffffff 0%, 
                #b9d9ff 25%, 
                #ffffff 50%, 
                #b9d9ff 75%, 
                #ffffff 100%
            );
            background-size: 200% auto;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            animation: shimmerText 4s linear infinite;
        }
 
        .text-shadow-premium {
            text-shadow: 
                0 2px 4px rgba(0, 0, 0, 0.3),
                0 4px 8px rgba(0, 0, 0, 0.2),
                0 8px 16px rgba(0, 0, 0, 0.1);
        }
 
        /* Card 3D Effect */
        .card-3d {
            transform-style: preserve-3d;
            transition: transform 0.6s var(--transition-expo);
        }
 
        .card-3d:hover {
            transform: perspective(1000px) rotateY(-5deg) rotateX(5deg) translateZ(20px);
        }
 
        .card-3d-content {
            transform: translateZ(30px);
            transition: transform 0.6s var(--transition-expo);
        }
 
        .card-3d:hover .card-3d-content {
            transform: translateZ(50px);
        }
 
        /* Button Premium */
        .btn-premium {
            position: relative;
            overflow: hidden;
            background: linear-gradient(135deg, #0066cc 0%, #0052a3 100%);
            transition: all 0.4s var(--transition-expo);
            box-shadow: 
                0 4px 15px rgba(0, 102, 204, 0.3),
                inset 0 1px 0 rgba(255, 255, 255, 0.2);
        }
 
        .btn-premium::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, 
                transparent 0%, 
                rgba(255, 255, 255, 0.3) 50%, 
                transparent 100%
            );
            transition: left 0.6s var(--transition-expo);
        }
 
        .btn-premium::after {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: inherit;
            padding: 2px;
            background: linear-gradient(135deg, 
                rgba(255, 255, 255, 0.4) 0%, 
                transparent 50%,
                rgba(255, 255, 255, 0.1) 100%
            );
            -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
            mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
            -webkit-mask-composite: xor;
            mask-composite: exclude;
        }
 
        .btn-premium:hover {
            transform: translateY(-3px);
            box-shadow: 
                0 8px 25px rgba(0, 102, 204, 0.4),
                0 15px 50px rgba(0, 102, 204, 0.2),
                inset 0 1px 0 rgba(255, 255, 255, 0.3);
        }
 
        .btn-premium:hover::before {
            left: 100%;
        }
 
        .btn-outline-premium {
            position: relative;
            background: transparent;
            border: 2px solid rgba(255, 255, 255, 0.3);
            transition: all 0.4s var(--transition-expo);
            overflow: hidden;
        }
 
        .btn-outline-premium::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, 
                rgba(255, 255, 255, 0.1) 0%, 
                rgba(255, 255, 255, 0.05) 100%
            );
            opacity: 0;
            transition: opacity 0.4s var(--transition-expo);
        }
 
        .btn-outline-premium:hover {
            border-color: rgba(255, 255, 255, 0.8);
            box-shadow: 0 0 30px rgba(255, 255, 255, 0.1);
        }
 
        .btn-outline-premium:hover::before {
            opacity: 1;
        }
 
        /* Input Premium */
        .input-premium {
            background: linear-gradient(135deg, 
                rgba(255, 255, 255, 1) 0%, 
                rgba(248, 246, 243, 1) 100%
            );
            border: 2px solid #efebe6;
            transition: all 0.4s var(--transition-expo);
            box-shadow: 
                0 2px 8px rgba(0, 0, 0, 0.05),
                inset 0 1px 0 rgba(255, 255, 255, 0.8);
        }
 
        .input-premium:focus {
            border-color: #0066cc;
            box-shadow: 
                0 0 0 4px rgba(0, 102, 204, 0.1),
                0 4px 16px rgba(0, 102, 204, 0.15),
                inset 0 1px 0 rgba(255, 255, 255, 0.8);
        }
 
        /* Scroll Reveal */
        .reveal {
            opacity: 0;
            transform: translateY(60px);
            transition: all 1s var(--transition-expo);
        }
 
        .reveal.active {
            opacity: 1;
            transform: translateY(0);
        }
 
        .reveal-left {
            opacity: 0;
            transform: translateX(-80px);
            transition: all 1s var(--transition-expo);
        }
 
        .reveal-left.active {
            opacity: 1;
            transform: translateX(0);
        }
 
        .reveal-right {
            opacity: 0;
            transform: translateX(80px);
            transition: all 1s var(--transition-expo);
        }
 
        .reveal-right.active {
            opacity: 1;
            transform: translateX(0);
        }
 
        .reveal-scale {
            opacity: 0;
            transform: scale(0.85);
            transition: all 1s var(--transition-expo);
        }
 
        .reveal-scale.active {
            opacity: 1;
            transform: scale(1);
        }
 
        /* Service Card Hover */
        .service-card-premium {
            position: relative;
            background: linear-gradient(180deg, 
                rgba(255, 255, 255, 1) 0%, 
                rgba(248, 246, 243, 1) 100%
            );
            border: 1px solid rgba(0, 102, 204, 0.1);
            transition: all 0.6s var(--transition-expo);
            overflow: hidden;
        }
 
        .service-card-premium::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, 
                #0066cc 0%, 
                #36a3ff 50%, 
                #0066cc 100%
            );
            transform: scaleX(0);
            transform-origin: left;
            transition: transform 0.6s var(--transition-expo);
        }
 
        .service-card-premium::after {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(
                circle at var(--mouse-x, 50%) var(--mouse-y, 50%),
                rgba(0, 102, 204, 0.08) 0%,
                transparent 50%
            );
            opacity: 0;
            transition: opacity 0.4s var(--transition-expo);
        }
 
        .service-card-premium:hover {
            transform: translateY(-12px);
            box-shadow: 
                0 25px 50px -12px rgba(0, 102, 204, 0.25),
                0 12px 24px -8px rgba(0, 102, 204, 0.15);
            border-color: rgba(0, 102, 204, 0.2);
        }
 
        .service-card-premium:hover::before {
            transform: scaleX(1);
        }
 
        .service-card-premium:hover::after {
            opacity: 1;
        }
 
        /* Icon Container */
        .icon-container-premium {
            position: relative;
            background: linear-gradient(135deg, #0066cc 0%, #0052a3 100%);
            box-shadow: 
                0 8px 24px rgba(0, 102, 204, 0.3),
                inset 0 1px 0 rgba(255, 255, 255, 0.2);
            transition: all 0.6s var(--transition-expo);
        }
 
        .icon-container-premium::before {
            content: '';
            position: absolute;
            inset: -2px;
            border-radius: inherit;
            background: linear-gradient(135deg, 
                rgba(255, 255, 255, 0.3) 0%, 
                transparent 50%
            );
            z-index: -1;
        }
 
        .service-card-premium:hover .icon-container-premium {
            transform: scale(1.1) rotate(5deg);
            box-shadow: 
                0 12px 32px rgba(0, 102, 204, 0.4),
                inset 0 1px 0 rgba(255, 255, 255, 0.3);
        }
 
        /* Process Step */
        .process-step-premium {
            position: relative;
        }
 
        .process-step-premium::before {
            content: '';
            position: absolute;
            left: 28px;
            top: 80px;
            bottom: -40px;
            width: 2px;
            background: linear-gradient(180deg, 
                #0066cc 0%, 
                #36a3ff 50%, 
                transparent 100%
            );
            opacity: 0.5;
        }
 
        .process-step-premium:last-child::before {
            display: none;
        }
 
        .process-number {
            position: relative;
            background: linear-gradient(135deg, #0066cc 0%, #0052a3 100%);
            box-shadow: 
                0 8px 24px rgba(0, 102, 204, 0.3),
                inset 0 2px 0 rgba(255, 255, 255, 0.2);
            transition: all 0.5s var(--transition-expo);
        }
 
        .process-number::before {
            content: '';
            position: absolute;
            inset: -4px;
            border-radius: inherit;
            border: 2px solid rgba(0, 102, 204, 0.2);
            animation: rotateBorder 20s linear infinite;
        }
 
        .process-step-premium:hover .process-number {
            transform: scale(1.15);
            box-shadow: 
                0 12px 32px rgba(0, 102, 204, 0.4),
                0 0 0 8px rgba(0, 102, 204, 0.1);
        }
 
        /* Decorative Elements */
        .decorative-circle {
            position: absolute;
            border-radius: 50%;
            background: linear-gradient(135deg, 
                rgba(0, 102, 204, 0.1) 0%, 
                rgba(54, 163, 255, 0.05) 100%
            );
            filter: blur(60px);
        }
 
        .greek-frieze {
            background: linear-gradient(90deg, transparent, rgba(0, 102, 204, 0.08) 20%, rgba(0, 102, 204, 0.12) 50%, rgba(0, 102, 204, 0.08) 80%, transparent);
        }
 
        /* Scrollbar */
        ::-webkit-scrollbar { width: 10px; }
        ::-webkit-scrollbar-track { 
            background: linear-gradient(180deg, #f8f6f3 0%, #efebe6 100%);
        }
        ::-webkit-scrollbar-thumb { 
            background: linear-gradient(180deg, #0066cc 0%, #0052a3 100%); 
            border-radius: 5px;
            border: 2px solid #f8f6f3;
        }
        ::-webkit-scrollbar-thumb:hover { 
            background: linear-gradient(180deg, #0052a3 0%, #054380 100%);
        }
 
        /* Selection */
        ::selection {
            background: rgba(0, 102, 204, 0.2);
            color: #071f33;
        }

        .assistant-fab {
            position: fixed;
            right: 1rem;
            bottom: 1rem;
            width: 3.75rem;
            height: 3.75rem;
            border-radius: 9999px;
            border: 1px solid rgba(255, 255, 255, 0.28);
            background: linear-gradient(135deg, #0c88f0 0%, #0052a3 100%);
            color: #ffffff;
            box-shadow: 0 20px 44px rgba(0, 82, 163, 0.34);
            z-index: 80;
            transition: transform .24s ease, box-shadow .24s ease, opacity .24s ease;
        }

        .assistant-fab:hover,
        .assistant-fab:focus-visible {
            transform: translateY(-2px) scale(1.02);
            box-shadow: 0 24px 52px rgba(0, 82, 163, 0.42);
        }

        .assistant-modal {
            position: fixed;
            right: 1rem;
            bottom: 5.75rem;
            width: min(24rem, calc(100vw - 1.5rem));
            max-height: min(80vh, 44rem);
            border-radius: 1rem;
            border: 1px solid rgba(5, 67, 128, 0.15);
            overflow: hidden;
            background: #ffffff;
            box-shadow: 0 30px 80px rgba(7, 31, 51, 0.28);
            z-index: 80;
            transform-origin: bottom right;
            transition: transform .24s ease, opacity .24s ease, visibility .24s ease;
        }

        .assistant-hidden {
            opacity: 0;
            transform: scale(.94) translateY(10px);
            visibility: hidden;
            pointer-events: none;
        }

        .assistant-header {
            background: linear-gradient(135deg, #071f33 0%, #0052a3 60%, #0c88f0 100%);
            color: #ffffff;
        }

        .assistant-messages {
            max-height: 21rem;
            overflow-y: auto;
            background: linear-gradient(180deg, #f7fbff 0%, #ffffff 100%);
        }

        .assistant-bubble-user {
            background: linear-gradient(135deg, #0066cc 0%, #0052a3 100%);
            color: #ffffff;
        }

        .assistant-bubble-ai {
            background: #eef6ff;
            color: #0a3a5c;
            border: 1px solid rgba(12, 136, 240, 0.2);
        }

        .assistant-suggestions button {
            border: 1px solid rgba(12, 136, 240, 0.22);
            color: #054380;
            background: #ffffff;
            transition: background .2s ease, color .2s ease;
        }

        .assistant-suggestions button:hover,
        .assistant-suggestions button:focus-visible {
            background: #eef6ff;
        }

        .assistant-clear-btn {
            border: 1px solid rgba(220, 38, 38, 0.28);
            background: #ffffff;
            color: #991b1b;
            transition: all .2s ease;
        }

        .assistant-clear-btn:hover,
        .assistant-clear-btn:focus-visible {
            background: #fff1f2;
            border-color: rgba(220, 38, 38, 0.42);
            color: #7f1d1d;
        }

        .assistant-clear-btn:active {
            transform: translateY(1px);
            background: #ffe4e6;
        }

        .assistant-clear-btn:disabled {
            cursor: not-allowed;
            opacity: .55;
            background: #f8fafc;
            color: #94a3b8;
            border-color: rgba(148, 163, 184, 0.38);
        }

        .assistant-confirm-backdrop {
            position: absolute;
            inset: 0;
            background: rgba(7, 31, 51, 0.52);
            backdrop-filter: blur(2px);
            z-index: 15;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }

        .assistant-confirm-modal {
            width: 100%;
            max-width: 20rem;
            border-radius: .9rem;
            border: 1px solid rgba(5, 67, 128, 0.15);
            background: #ffffff;
            box-shadow: 0 20px 55px rgba(7, 31, 51, 0.2);
        }

        .assistant-typing span {
            width: .4rem;
            height: .4rem;
            border-radius: 9999px;
            background: #0c88f0;
            animation: assistantTyping 1s infinite ease-in-out;
        }

        .assistant-typing span:nth-child(2) {
            animation-delay: .12s;
        }

        .assistant-typing span:nth-child(3) {
            animation-delay: .24s;
        }

        @keyframes assistantTyping {
            0%, 80%, 100% { transform: scale(.6); opacity: .5; }
            40% { transform: scale(1); opacity: 1; }
        }

        @media (max-width: 640px) {
            .assistant-fab {
                right: .75rem;
                bottom: .75rem;
            }

            .assistant-modal {
                right: .75rem;
                bottom: 5.3rem;
                width: calc(100vw - 1.5rem);
            }
        }
    </style>
</head>
<body class="font-body antialiased text-gray-800 bg-white overflow-x-hidden">
 
    <!-- Noise Overlay -->
    <div class="noise-overlay"></div>
 
    <!-- Custom Cursor -->
    <div class="custom-cursor" id="cursor"></div>
 
    <!-- Navigation -->
    <nav class="fixed top-0 left-0 right-0 z-50 transition-all duration-700" id="navbar">
        <div class="absolute inset-0 bg-ethos-950/80 backdrop-blur-xl border-b border-white/5 pointer-events-none opacity-0 transition-all duration-700" id="nav-bg"></div>
 
        <div class="relative max-w-7xl mx-auto px-6 lg:px-8">
            <div class="flex items-center justify-between h-24">
                <!-- Logo -->
                <a href="#" class="flex items-center gap-4 group">
                    <div class="relative w-14 h-14 flex items-center justify-center overflow-hidden rounded-xl bg-white/5 backdrop-blur-sm border border-white/10 group-hover:border-white/30 transition-colors duration-500">
                        <img src="{{ asset('assets/image/logo.png') }}" alt="ETHOS Logo" class="w-full h-full object-contain p-1 group-hover:scale-110 transition-transform duration-500">
                    </div>
                    <div class="flex flex-col">
                        <span class="font-display text-2xl font-semibold text-white tracking-[0.15em] group-hover:tracking-[0.2em] transition-all duration-500">ETHOS</span>
                        <span class="text-[10px] text-white/50 tracking-[0.4em] uppercase">Consultoría Estratégica</span>
                    </div>
                </a>
 
                <!-- Desktop Navigation -->
                <div class="hidden lg:flex items-center gap-12">
                    <a href="#problemas" class="relative text-white/70 hover:text-white text-sm tracking-widest uppercase transition-colors duration-300 group">
                        Problemas
                        <span class="absolute -bottom-2 left-0 w-0 h-0.5 bg-gradient-to-r from-ethos-400 to-ethos-600 group-hover:w-full transition-all duration-500"></span>
                    </a>
                    <a href="#metodologia" class="relative text-white/70 hover:text-white text-sm tracking-widest uppercase transition-colors duration-300 group">
                        Metodología
                        <span class="absolute -bottom-2 left-0 w-0 h-0.5 bg-gradient-to-r from-ethos-400 to-ethos-600 group-hover:w-full transition-all duration-500"></span>
                    </a>
                    <a href="#servicios" class="relative text-white/70 hover:text-white text-sm tracking-widest uppercase transition-colors duration-300 group">
                        Servicios
                        <span class="absolute -bottom-2 left-0 w-0 h-0.5 bg-gradient-to-r from-ethos-400 to-ethos-600 group-hover:w-full transition-all duration-500"></span>
                    </a>
                    <a href="#contacto" class="btn-outline-premium text-white px-8 py-3 rounded-lg text-sm font-semibold tracking-widest uppercase">
                        Contactar
                    </a>
                    <a href="/login" class="group inline-flex items-center gap-2 text-white/60 hover:text-white text-xs tracking-[0.25em] uppercase transition-colors duration-300" aria-label="Login">
                        <svg class="w-4 h-4 opacity-80 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm2-10V7a4 4 0 118 0v4"/>
                        </svg>
                        <span class="relative">
                            Login
                            <span class="absolute -bottom-2 left-0 w-0 h-px bg-gradient-to-r from-ethos-400 to-ethos-600 group-hover:w-full transition-all duration-500"></span>
                        </span>
                    </a>
                </div>
 
                <!-- Mobile Menu Button -->
                <button class="lg:hidden text-white p-3 hover:bg-white/10 rounded-lg transition-colors" id="mobile-menu-btn">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
            </div>
        </div>
 
        <!-- Mobile Menu -->
        <div class="hidden lg:hidden glass-dark-premium" id="mobile-menu">
            <div class="px-6 py-8 space-y-6">
                <a href="#problemas" class="block text-white/80 hover:text-white py-3 text-lg tracking-wide transition-colors">Problemas</a>
                <a href="#metodologia" class="block text-white/80 hover:text-white py-3 text-lg tracking-wide transition-colors">Metodología</a>
                <a href="#servicios" class="block text-white/80 hover:text-white py-3 text-lg tracking-wide transition-colors">Servicios</a>
                <a href="#contacto" class="block text-white font-semibold py-3 text-lg tracking-wide">Contactar</a>
                <a href="/login" class="inline-flex items-center gap-3 text-white/70 hover:text-white py-3 text-sm tracking-[0.25em] uppercase transition-colors" aria-label="Login">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm2-10V7a4 4 0 118 0v4"/>
                    </svg>
                    Login
                </a>
            </div>
        </div>
    </nav>
 
    <!-- Hero Section -->
    <section class="relative min-h-screen flex items-center overflow-hidden bg-gradient-to-br from-ethos-950 via-ethos-900 to-ethos-800">
        <!-- Animated Background -->
        <div class="absolute inset-0">
            <!-- Subtle radial gradient background -->
            <div class="absolute inset-0 opacity-[0.06]" style="background: radial-gradient(ellipse at 30% 20%, rgba(54,163,255,0.3) 0%, transparent 60%), radial-gradient(ellipse at 70% 80%, rgba(0,102,204,0.2) 0%, transparent 50%);"></div>
 
            <!-- Decorative circles -->
            <div class="decorative-circle w-[600px] h-[600px] -top-48 -right-48 animate-float"></div>
            <div class="decorative-circle w-[500px] h-[500px] -bottom-32 -left-32 animate-float" style="animation-delay: 3s;"></div>
            <div class="decorative-circle w-[400px] h-[400px] top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 animate-morph"></div>
 
            <!-- Grid pattern -->
            <div class="absolute inset-0 meander-complex"></div>
        </div>
 
        <!-- Top line -->
        <div class="absolute top-0 left-0 right-0 h-px bg-gradient-to-r from-transparent via-ethos-400/40 to-transparent"></div>
 
        <!-- Greek Frieze -->
        <div class="absolute top-20 left-0 right-0 h-10 greek-frieze opacity-30"></div>
 
        <!-- Content -->
        <div class="relative z-10 max-w-7xl mx-auto px-6 lg:px-8 py-32 lg:py-40">
            <div class="grid lg:grid-cols-2 gap-16 lg:gap-24 items-center">
                <!-- Left Column -->
                <div class="max-w-2xl">
                    <!-- Greek Quote -->
                    <div class="flex items-center gap-6 mb-10 animate-hero-reveal">
                        <div class="flex items-center gap-2">
                            <div class="w-12 h-px bg-gradient-to-r from-ethos-400 to-transparent"></div>
                            <div class="w-2 h-2 rounded-full bg-ethos-400 animate-pulse"></div>
                        </div>
                        <span class="font-serif text-white/50 italic text-xl tracking-wide">ἦθος ἀνὴρ δαίμων</span>
                    </div>
 
                    <!-- Main Headline -->
                    <h1 class="font-display text-4xl md:text-5xl lg:text-6xl xl:text-7xl font-semibold text-white leading-[1.1] animate-hero-reveal delay-200">
                        Cuando la empresa crece más rápido que su estructura,
                        <span class="block mt-4 text-gradient-animated">pierdes control, tiempo y dinero.</span>
                    </h1>
 
                    <!-- Subheadline -->
                    <p class="mt-10 font-serif text-xl md:text-2xl text-white/70 leading-relaxed animate-hero-reveal delay-400">
                        En <strong class="text-white font-semibold not-italic">ETHOS</strong> diseñamos procesos inteligentes, auditoría estratégica y sistemas empresariales sólidos que te permiten operar con claridad.
                    </p>
 
                    <!-- CTAs -->
                    <div class="mt-12 flex flex-col sm:flex-row gap-5 animate-hero-reveal delay-600">
                        <a href="#servicios" class="btn-premium text-white font-semibold py-4 px-10 rounded-xl text-center tracking-wide inline-flex items-center justify-center gap-3">
                            Conoce nuestras soluciones
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                            </svg>
                        </a>
                        <a href="#contacto" class="btn-outline-premium text-white font-semibold py-4 px-10 rounded-xl text-center tracking-wide">
                            Hablar con un consultor
                        </a>
                    </div>
 
                    <!-- Stats -->
                    <div class="mt-20 grid grid-cols-3 gap-8 animate-hero-reveal delay-800">
                        <div class="text-center group">
                            <div class="font-display text-4xl md:text-5xl font-bold text-white group-hover:scale-110 transition-transform duration-500">15+</div>
                            <div class="text-sm text-white/40 mt-2 tracking-wide uppercase">Años</div>
                        </div>
                        <div class="text-center border-x border-white/10 group">
                            <div class="font-display text-4xl md:text-5xl font-bold text-white group-hover:scale-110 transition-transform duration-500">200+</div>
                            <div class="text-sm text-white/40 mt-2 tracking-wide uppercase">Empresas</div>
                        </div>
                        <div class="text-center group">
                            <div class="font-display text-4xl md:text-5xl font-bold text-white group-hover:scale-110 transition-transform duration-500">98%</div>
                            <div class="text-sm text-white/40 mt-2 tracking-wide uppercase">Satisfacción</div>
                        </div>
                    </div>
                </div>
 
                <!-- Right Column - Visual -->
                <div class="hidden lg:block relative animate-fade-scale delay-500">
                    <!-- Decorative Frame -->
                    <div class="relative">
                        <!-- Corner decorations -->
                        <div class="absolute -top-8 -left-8 w-32 h-32">
                            <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-ethos-400 to-transparent"></div>
                            <div class="absolute top-0 left-0 w-1 h-full bg-gradient-to-b from-ethos-400 to-transparent"></div>
                        </div>
                        <div class="absolute -bottom-8 -right-8 w-32 h-32">
                            <div class="absolute bottom-0 right-0 w-full h-1 bg-gradient-to-l from-ethos-400 to-transparent"></div>
                            <div class="absolute bottom-0 right-0 w-1 h-full bg-gradient-to-t from-ethos-400 to-transparent"></div>
                        </div>
 
                        <!-- Main Image Container -->
                        <div class="relative rounded-2xl overflow-hidden shadow-2xl animate-pulse-glow">
                            <img src="https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?w=800&q=80" 
                                 alt="Edificio corporativo moderno representando la consultoría empresarial de ETHOS" 
                                 title="Diseño de estructuras organizacionales"
                                 loading="lazy"
                                 class="w-full h-[550px] object-cover">
 
                            <!-- Overlay -->
                            <div class="absolute inset-0 bg-gradient-to-t from-ethos-950 via-ethos-950/20 to-transparent"></div>
 
                            <!-- Floating Info Card -->
                            <div class="absolute bottom-8 left-8 right-8 glass-premium rounded-xl p-6 transform hover:scale-[1.02] transition-transform duration-500">
                                <div class="flex items-center gap-5">
                                    <div class="w-14 h-14 bg-gradient-to-br from-ethos-600 to-ethos-700 rounded-xl flex items-center justify-center shadow-lg">
                                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="font-display font-semibold text-ethos-900 text-lg">Estructura sólida</div>
                                        <div class="text-sm text-gray-500 font-serif">Fundamentos que perduran en el tiempo</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
 
        <!-- Scroll Indicator -->
        <div class="absolute bottom-10 left-1/2 -translate-x-1/2 flex flex-col items-center gap-3 animate-bounce">
            <span class="text-white/30 text-xs tracking-[0.3em] uppercase">Descubre</span>
            <div class="w-6 h-10 border-2 border-white/30 rounded-full flex items-start justify-center p-1.5">
                <div class="w-1.5 h-3 bg-white/50 rounded-full animate-bounce"></div>
            </div>
        </div>
 
        <!-- Bottom line -->
        <div class="absolute bottom-0 left-0 right-0 h-px bg-gradient-to-r from-transparent via-ethos-400/30 to-transparent"></div>
    </section>
 
    <!-- Trust Strip -->
    <section class="py-8 bg-ethos-950 border-y border-white/5 relative overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-r from-ethos-950 via-ethos-900 to-ethos-950"></div>
        <div class="relative max-w-7xl mx-auto px-6 lg:px-8">
            <div class="flex flex-col md:flex-row items-center justify-center gap-8 md:gap-16">
                <span class="text-white/30 text-xs tracking-[0.4em] uppercase font-display">Confían en nosotros</span>
                <div class="flex items-center gap-12 md:gap-16">
                    <div class="text-center">
                        <div class="text-white/50 font-display text-sm tracking-wider">Manufactura</div>
                    </div>
                    <div class="w-px h-6 bg-white/10"></div>
                    <div class="text-center">
                        <div class="text-white/50 font-display text-sm tracking-wider">Retail</div>
                    </div>
                    <div class="w-px h-6 bg-white/10"></div>
                    <div class="text-center">
                        <div class="text-white/50 font-display text-sm tracking-wider">Tecnología</div>
                    </div>
                    <div class="w-px h-6 bg-white/10"></div>
                    <div class="text-center">
                        <div class="text-white/50 font-display text-sm tracking-wider">Salud</div>
                    </div>
                    <div class="w-px h-6 bg-white/10"></div>
                    <div class="text-center">
                        <div class="text-white/50 font-display text-sm tracking-wider">Finanzas</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Problems Section -->
    <section id="problemas" class="py-28 lg:py-36 bg-gradient-to-b from-marble-50 to-white relative overflow-hidden">
        <!-- Decorative background -->
        <div class="absolute inset-0 meander-complex opacity-30"></div>
        <div class="decorative-circle w-[500px] h-[500px] -top-64 -right-64 opacity-50"></div>
 
        <div class="relative max-w-7xl mx-auto px-6 lg:px-8">
            <!-- Section Header -->
            <div class="text-center max-w-4xl mx-auto mb-20">
                <div class="inline-flex items-center gap-4 mb-8 reveal">
                    <div class="w-16 h-px bg-gradient-to-r from-transparent to-ethos-300"></div>
                    <span class="font-display text-sm tracking-[0.4em] text-ethos-600 uppercase">Diagnóstico Empresarial</span>
                    <div class="w-16 h-px bg-gradient-to-l from-transparent to-ethos-300"></div>
                </div>
 
                <h2 class="font-display text-4xl md:text-5xl lg:text-6xl font-semibold text-ethos-900 leading-tight reveal delay-200">
                    ¿Tu empresa presenta alguno de estos escenarios?
                </h2>
 
                <p class="mt-8 font-serif text-xl text-gray-600 leading-relaxed max-w-2xl mx-auto reveal delay-300">
                    Identificamos los desafíos más comunes que frenan el crecimiento empresarial y diseñamos soluciones a medida.
                </p>
            </div>
 
            <!-- Problems Grid -->
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Problem 1 -->
                <div class="service-card-premium rounded-2xl p-8 lg:p-10 reveal delay-100" data-cursor-hover>
                    <div class="relative z-10">
                        <div class="icon-container-premium w-16 h-16 rounded-2xl flex items-center justify-center mb-8">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                        </div>
 
                        <h3 class="font-display text-xl font-semibold text-ethos-900 mb-4">Procesos improvisados</h3>
 
                        <p class="font-serif text-gray-600 leading-relaxed">
                            Falta de procedimientos estandarizados que generan inconsistencias operativas y resultados impredecibles.
                        </p>
 
                        <div class="mt-8 pt-6 border-t border-marble-200">
                            <div class="flex items-center gap-2 text-ethos-600 text-sm font-medium">
                                <span class="w-2 h-2 rounded-full bg-ethos-600"></span>
                                Alto impacto operacional
                            </div>
                        </div>
                    </div>
                </div>
 
                <!-- Problem 2 -->
                <div class="service-card-premium rounded-2xl p-8 lg:p-10 reveal delay-200" data-cursor-hover>
                    <div class="relative z-10">
                        <div class="icon-container-premium w-16 h-16 rounded-2xl flex items-center justify-center mb-8">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </div>
 
                        <h3 class="font-display text-xl font-semibold text-ethos-900 mb-4">Funciones poco claras</h3>
 
                        <p class="font-serif text-gray-600 leading-relaxed">
                            Roles mal definidos que provocan duplicación de tareas, inefficiencias y conflictos internos.
                        </p>
 
                        <div class="mt-8 pt-6 border-t border-marble-200">
                            <div class="flex items-center gap-2 text-ethos-600 text-sm font-medium">
                                <span class="w-2 h-2 rounded-full bg-ethos-600"></span>
                                Afecta cultura organizacional
                            </div>
                        </div>
                    </div>
                </div>
 
                <!-- Problem 3 -->
                <div class="service-card-premium rounded-2xl p-8 lg:p-10 reveal delay-300" data-cursor-hover>
                    <div class="relative z-10">
                        <div class="icon-container-premium w-16 h-16 rounded-2xl flex items-center justify-center mb-8">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 0h4m-4 0V5a2 2 0 012-2h2a2 2 0 012 2v4h-4m0 0v6a2 2 0 01-2 2H8a2 2 0 01-2-2v-6h4m0 0V9a2 2 0 012-2h2a2 2 0 012 2v2h-4"/>
                            </svg>
                        </div>
 
                        <h3 class="font-display text-xl font-semibold text-ethos-900 mb-4">Riesgos fiscales y legales</h3>
 
                        <p class="font-serif text-gray-600 leading-relaxed">
                            Exposición a sanciones y problemas legales por falta de cumplimiento normativo.
                        </p>
 
                        <div class="mt-8 pt-6 border-t border-marble-200">
                            <div class="flex items-center gap-2 text-bronze-500 text-sm font-medium">
                                <span class="w-2 h-2 rounded-full bg-bronze-500"></span>
                                Riesgo crítico
                            </div>
                        </div>
                    </div>
                </div>
 
                <!-- Problem 4 -->
                <div class="service-card-premium rounded-2xl p-8 lg:p-10 reveal delay-400" data-cursor-hover>
                    <div class="relative z-10">
                        <div class="icon-container-premium w-16 h-16 rounded-2xl flex items-center justify-center mb-8">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M12 7h.01M15 7h.01M15 14h.01M12 14h.01m3 3h.01M12 17h.01M9 17h.01"/>
                            </svg>
                        </div>
 
                        <h3 class="font-display text-xl font-semibold text-ethos-900 mb-4">Finanzas sin control</h3>
 
                        <p class="font-serif text-gray-600 leading-relaxed">
                            Falta de visibilidad sobre el estado financiero real de la empresa.
                        </p>
 
                        <div class="mt-8 pt-6 border-t border-marble-200">
                            <div class="flex items-center gap-2 text-ethos-600 text-sm font-medium">
                                <span class="w-2 h-2 rounded-full bg-ethos-600"></span>
                                Impacto en decisiones
                            </div>
                        </div>
                    </div>
                </div>
 
                <!-- Problem 5 -->
                <div class="service-card-premium rounded-2xl p-8 lg:p-10 reveal delay-500" data-cursor-hover>
                    <div class="relative z-10">
                        <div class="icon-container-premium w-16 h-16 rounded-2xl flex items-center justify-center mb-8">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                        </div>
 
                        <h3 class="font-display text-xl font-semibold text-ethos-900 mb-4">Decisiones "a ciegas"</h3>
 
                        <p class="font-serif text-gray-600 leading-relaxed">
                            Ausencia de datos confiables para tomar decisiones estratégicas informadas.
                        </p>
 
                        <div class="mt-8 pt-6 border-t border-marble-200">
                            <div class="flex items-center gap-2 text-ethos-600 text-sm font-medium">
                                <span class="w-2 h-2 rounded-full bg-ethos-600"></span>
                                Limita crecimiento
                            </div>
                        </div>
                    </div>
                </div>
 
                <!-- Problem 6 -->
                <div class="service-card-premium rounded-2xl p-8 lg:p-10 reveal delay-600" data-cursor-hover>
                    <div class="relative z-10">
                        <div class="icon-container-premium w-16 h-16 rounded-2xl flex items-center justify-center mb-8">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                        </div>
 
                        <h3 class="font-display text-xl font-semibold text-ethos-900 mb-4">Equipos estancados</h3>
 
                        <p class="font-serif text-gray-600 leading-relaxed">
                            Mucho esfuerzo sin resultados tangibles por falta de dirección clara.
                        </p>
 
                        <div class="mt-8 pt-6 border-t border-marble-200">
                            <div class="flex items-center gap-2 text-ethos-600 text-sm font-medium">
                                <span class="w-2 h-2 rounded-full bg-ethos-600"></span>
                                Desgasta recursos
                            </div>
                        </div>
                    </div>
                </div>
            </div>
 
            <!-- CTA -->
            <div class="mt-20 text-center reveal delay-700">
                <a href="#contacto" class="btn-premium inline-flex items-center gap-3 text-white font-semibold py-4 px-12 rounded-xl tracking-wide">
                    Hablar con un consultor
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                    </svg>
                </a>
            </div>
        </div>
    </section>
 
    <!-- Elegant Divider -->
    <div class="relative h-px bg-gradient-to-r from-transparent via-ethos-200 to-transparent">
        <div class="absolute left-1/2 -translate-x-1/2 -top-3 w-6 h-6 bg-marble-50 flex items-center justify-center">
            <div class="w-1.5 h-1.5 bg-ethos-400 rounded-full"></div>
        </div>
    </div>
 
    <!-- Methodology Section -->
    <section id="metodologia" class="py-28 lg:py-36 bg-white relative overflow-hidden">
        <div class="absolute inset-0 meander-complex opacity-20"></div>
        <div class="decorative-circle w-[600px] h-[600px] -bottom-64 -left-64"></div>
 
        <div class="relative max-w-7xl mx-auto px-6 lg:px-8">
            <div class="grid lg:grid-cols-2 gap-20 lg:gap-32 items-center">
                <!-- Left Content -->
                <div>
                    <div class="inline-flex items-center gap-4 mb-8 reveal-left">
                        <div class="w-16 h-px bg-gradient-to-r from-ethos-300 to-transparent"></div>
                        <span class="font-display text-sm tracking-[0.4em] text-ethos-600 uppercase">Nuestro Enfoque</span>
                    </div>
 
                    <h2 class="font-display text-4xl md:text-5xl lg:text-6xl font-semibold text-ethos-900 leading-tight reveal-left delay-200">
                        Servicios personalizados con un equipo de expertos
                    </h2>
 
                    <p class="mt-10 font-serif text-xl text-gray-600 leading-relaxed reveal-left delay-300">
                        Identificamos riesgos y diseñamos sistemas internos que responden a tus 
                        <span class="text-ethos-700 font-semibold">necesidades específicas</span>, 
                        a tu <span class="text-ethos-700 font-semibold">etapa de crecimiento</span> 
                        y a tus <span class="text-ethos-700 font-semibold">objetivos estratégicos</span>.
                    </p>
 
                    <!-- Methodology Box -->
                    <div class="mt-12 relative reveal-left delay-400">
                        <div class="absolute -left-6 top-0 bottom-0 w-1.5 bg-gradient-to-b from-ethos-600 via-ethos-400 to-ethos-300 rounded-full"></div>
 
                        <div class="glass-premium rounded-2xl p-8 lg:p-10">
                            <h3 class="font-display text-2xl font-semibold text-ethos-900 mb-6">Metodología orientada a resultados</h3>
 
                            <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-center gap-4 lg:gap-6">
                                <div class="flex-1 w-full text-center p-4 lg:p-6 bg-gradient-to-br from-ethos-50 to-white rounded-xl border border-ethos-100 hover:shadow-lg transition-shadow duration-500">
                                    <div class="font-display text-2xl lg:text-3xl tracking-tight font-bold text-ethos-700">Estructura</div>
                                    <div class="text-xs sm:text-sm text-gray-500 mt-2 font-serif">Técnica</div>
                                </div>
                                <div class="text-2xl lg:text-3xl text-ethos-300 font-light flex-shrink-0 text-center">+</div>
                                <div class="flex-1 w-full text-center p-4 lg:p-6 bg-gradient-to-br from-ethos-50 to-white rounded-xl border border-ethos-100 hover:shadow-lg transition-shadow duration-500">
                                    <div class="font-display text-2xl lg:text-3xl tracking-tight font-bold text-ethos-700">Habilidades</div>
                                    <div class="text-xs sm:text-sm text-gray-500 mt-2 font-serif">Sociales</div>
                                </div>
                            </div>
 
                            <p class="mt-8 text-gray-500 italic font-serif">
                                Combinamos rigor analítico con sensibilidad humana para implementaciones exitosas.
                            </p>
                        </div>
                    </div>
                </div>
 
                <!-- Right Content - Process -->
                <div class="relative reveal-right">
                    <div class="decorative-circle w-[400px] h-[400px] -top-32 -right-32"></div>
 
                    <div class="relative glass-premium rounded-3xl p-8 lg:p-12 shadow-2xl">
                        <h3 class="font-display text-2xl font-semibold text-ethos-900 mb-12 pb-6 border-b border-marble-200">
                            Nuestro Proceso
                        </h3>
 
                        <div class="space-y-10">
                            <!-- Step I -->
                            <div class="process-step-premium flex gap-6 group cursor-pointer" data-cursor-hover>
                                <div class="process-number flex-shrink-0 w-16 h-16 rounded-xl flex items-center justify-center text-white font-display font-bold text-xl">
                                    I
                                </div>
                                <div class="flex-1 pt-3">
                                    <h4 class="font-display font-semibold text-ethos-900 text-lg group-hover:text-ethos-600 transition-colors">Diagnóstico estratégico y técnico</h4>
                                    <p class="text-sm text-gray-500 mt-2 font-serif">Análisis profundo de tu situación actual</p>
                                </div>
                            </div>
 
                            <!-- Step II -->
                            <div class="process-step-premium flex gap-6 group cursor-pointer" data-cursor-hover>
                                <div class="process-number flex-shrink-0 w-16 h-16 rounded-xl flex items-center justify-center text-white font-display font-bold text-xl" style="background: linear-gradient(135deg, #0052a3 0%, #0066cc 100%);">
                                    II
                                </div>
                                <div class="flex-1 pt-3">
                                    <h4 class="font-display font-semibold text-ethos-900 text-lg group-hover:text-ethos-600 transition-colors">Auditoría y análisis de riesgos</h4>
                                    <p class="text-sm text-gray-500 mt-2 font-serif">Identificación de vulnerabilidades críticas</p>
                                </div>
                            </div>
 
                            <!-- Step III -->
                            <div class="process-step-premium flex gap-6 group cursor-pointer" data-cursor-hover>
                                <div class="process-number flex-shrink-0 w-16 h-16 rounded-xl flex items-center justify-center text-white font-display font-bold text-xl" style="background: linear-gradient(135deg, #054380 0%, #0052a3 100%);">
                                    III
                                </div>
                                <div class="flex-1 pt-3">
                                    <h4 class="font-display font-semibold text-ethos-900 text-lg group-hover:text-ethos-600 transition-colors">Diseño de estructura y procesos</h4>
                                    <p class="text-sm text-gray-500 mt-2 font-serif">Arquitectura organizacional optimizada</p>
                                </div>
                            </div>
 
                            <!-- Step IV -->
                            <div class="process-step-premium flex gap-6 group cursor-pointer" data-cursor-hover>
                                <div class="process-number flex-shrink-0 w-16 h-16 rounded-xl flex items-center justify-center text-white font-display font-bold text-xl" style="background: linear-gradient(135deg, #0a3a5c 0%, #054380 100%);">
                                    IV
                                </div>
                                <div class="flex-1 pt-3">
                                    <h4 class="font-display font-semibold text-ethos-900 text-lg group-hover:text-ethos-600 transition-colors">Implementación guiada</h4>
                                    <p class="text-sm text-gray-500 mt-2 font-serif">Acompañamiento en cada paso del cambio</p>
                                </div>
                            </div>
 
                            <!-- Step V -->
                            <div class="process-step-premium flex gap-6 group cursor-pointer" data-cursor-hover>
                                <div class="process-number flex-shrink-0 w-16 h-16 rounded-xl flex items-center justify-center text-white font-display font-bold text-xl" style="background: linear-gradient(135deg, #071f33 0%, #0a3a5c 100%);">
                                    V
                                </div>
                                <div class="flex-1 pt-3">
                                    <h4 class="font-display font-semibold text-ethos-900 text-lg group-hover:text-ethos-600 transition-colors">Seguimiento y mejora continua</h4>
                                    <p class="text-sm text-gray-500 mt-2 font-serif">Monitoreo y ajustes permanentes</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
 
    <!-- Services Section -->
    <section id="servicios" class="py-28 lg:py-36 bg-gradient-to-b from-marble-50 to-white relative overflow-hidden">
        <div class="absolute inset-0 meander-complex opacity-30"></div>
 
        <div class="relative max-w-7xl mx-auto px-6 lg:px-8">
            <!-- Section Header -->
            <div class="text-center max-w-4xl mx-auto mb-20">
                <div class="inline-flex items-center gap-4 mb-8 reveal">
                    <div class="w-16 h-px bg-gradient-to-r from-transparent to-ethos-300"></div>
                    <span class="font-display text-sm tracking-[0.4em] text-ethos-600 uppercase">Servicios</span>
                    <div class="w-16 h-px bg-gradient-to-l from-transparent to-ethos-300"></div>
                </div>
 
                <h2 class="font-display text-4xl md:text-5xl lg:text-6xl font-semibold text-ethos-900 reveal delay-200">
                    Pilares de la Intervención
                </h2>
 
                <p class="mt-8 font-serif text-xl text-gray-600 reveal delay-300">
                    Seis ejes de trabajo diseñados para transformar tu organización
                </p>
            </div>
 
            <!-- Services Grid -->
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8 lg:gap-10">
                <!-- Service 1 -->
                <div class="service-card-premium rounded-2xl p-8 lg:p-10 reveal delay-100" data-cursor-hover>
                    <div class="relative z-10">
                        <div class="icon-container-premium w-16 h-16 rounded-2xl flex items-center justify-center mb-8">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                            </svg>
                        </div>
 
                        <h3 class="font-display text-xl font-semibold text-ethos-900 mb-4">Auditoría interna y fiscal</h3>
 
                        <p class="font-serif text-gray-600 leading-relaxed">
                            Evaluación exhaustiva de procesos internos y cumplimiento fiscal para garantizar transparencia y eficiencia operativa.
                        </p>
 
                        <div class="mt-8 pt-6 border-t border-marble-200">
                            <a href="#contacto" class="inline-flex items-center gap-2 text-ethos-600 font-medium text-sm group-hover:gap-4 transition-all duration-300">
                                Más información
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
 
                <!-- Service 2 -->
                <div class="service-card-premium rounded-2xl p-8 lg:p-10 reveal delay-200" data-cursor-hover>
                    <div class="relative z-10">
                        <div class="icon-container-premium w-16 h-16 rounded-2xl flex items-center justify-center mb-8">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"/>
                            </svg>
                        </div>
 
                        <h3 class="font-display text-xl font-semibold text-ethos-900 mb-4">Diseño de procesos corporativos</h3>
 
                        <p class="font-serif text-gray-600 leading-relaxed">
                            Creación de flujos de trabajo optimizados que mejoran la productividad y reducen tiempos operativos.
                        </p>
 
                        <div class="mt-8 pt-6 border-t border-marble-200">
                            <a href="#contacto" class="inline-flex items-center gap-2 text-ethos-600 font-medium text-sm group-hover:gap-4 transition-all duration-300">
                                Más información
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
 
                <!-- Service 3 -->
                <div class="service-card-premium rounded-2xl p-8 lg:p-10 reveal delay-300" data-cursor-hover>
                    <div class="relative z-10">
                        <div class="icon-container-premium w-16 h-16 rounded-2xl flex items-center justify-center mb-8">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                        </div>
 
                        <h3 class="font-display text-xl font-semibold text-ethos-900 mb-4">Manuales operativos y de funciones</h3>
 
                        <p class="font-serif text-gray-600 leading-relaxed">
                            Documentación clara y completa que estandariza operaciones y define responsabilidades.
                        </p>
 
                        <div class="mt-8 pt-6 border-t border-marble-200">
                            <a href="#contacto" class="inline-flex items-center gap-2 text-ethos-600 font-medium text-sm group-hover:gap-4 transition-all duration-300">
                                Más información
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
 
                <!-- Service 4 -->
                <div class="service-card-premium rounded-2xl p-8 lg:p-10 reveal delay-400" data-cursor-hover>
                    <div class="relative z-10">
                        <div class="icon-container-premium w-16 h-16 rounded-2xl flex items-center justify-center mb-8">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
 
                        <h3 class="font-display text-xl font-semibold text-ethos-900 mb-4">Estructuración organizacional</h3>
 
                        <p class="font-serif text-gray-600 leading-relaxed">
                            Diseño de organigramas funcionales y definición de jerarquías para una gestión eficiente.
                        </p>
 
                        <div class="mt-8 pt-6 border-t border-marble-200">
                            <a href="#contacto" class="inline-flex items-center gap-2 text-ethos-600 font-medium text-sm group-hover:gap-4 transition-all duration-300">
                                Más información
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
 
                <!-- Service 5 -->
                <div class="service-card-premium rounded-2xl p-8 lg:p-10 reveal delay-500" data-cursor-hover>
                    <div class="relative z-10">
                        <div class="icon-container-premium w-16 h-16 rounded-2xl flex items-center justify-center mb-8">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                            </svg>
                        </div>
 
                        <h3 class="font-display text-xl font-semibold text-ethos-900 mb-4">Sistemas de control contable</h3>
 
                        <p class="font-serif text-gray-600 leading-relaxed">
                            Implementación de controles que aseguran la integridad de la información financiera.
                        </p>
 
                        <div class="mt-8 pt-6 border-t border-marble-200">
                            <a href="#contacto" class="inline-flex items-center gap-2 text-ethos-600 font-medium text-sm group-hover:gap-4 transition-all duration-300">
                                Más información
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
 
                <!-- Service 6 -->
                <div class="service-card-premium rounded-2xl p-8 lg:p-10 reveal delay-600" data-cursor-hover>
                    <div class="relative z-10">
                        <div class="icon-container-premium w-16 h-16 rounded-2xl flex items-center justify-center mb-8">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                        </div>
 
                        <h3 class="font-display text-xl font-semibold text-ethos-900 mb-4">Protocolos internos empresariales</h3>
 
                        <p class="font-serif text-gray-600 leading-relaxed">
                            Normativas y procedimientos que garantizan seguridad, calidad y cumplimiento.
                        </p>
 
                        <div class="mt-8 pt-6 border-t border-marble-200">
                            <a href="#contacto" class="inline-flex items-center gap-2 text-ethos-600 font-medium text-sm group-hover:gap-4 transition-all duration-300">
                                Más información
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
 
    <!-- CTA Section -->
    <section class="py-28 lg:py-36 bg-gradient-to-br from-ethos-950 via-ethos-900 to-ethos-800 relative overflow-hidden">
        <div class="absolute inset-0 meander-complex opacity-20"></div>
        <div class="absolute inset-0 bg-gradient-to-t from-ethos-950/50 to-transparent"></div>
        <div class="decorative-circle w-[500px] h-[500px] top-0 left-1/4"></div>
        <div class="decorative-circle w-[400px] h-[400px] bottom-0 right-1/4"></div>
        <div class="absolute top-0 left-0 right-0 h-px bg-gradient-to-r from-transparent via-ethos-400/30 to-transparent"></div>
        <div class="absolute bottom-0 left-0 right-0 h-px bg-gradient-to-r from-transparent via-ethos-400/30 to-transparent"></div>

        <div class="relative z-10 max-w-4xl mx-auto px-6 lg:px-8 text-center">
            <div class="glass-dark-premium rounded-3xl p-12 lg:p-20">
                <div class="font-serif text-ethos-300/60 italic text-lg mb-8 reveal">
                    "ἦθος ἀνὴρ δαίμων" — El carácter es destino
                </div>
                <h2 class="font-display text-3xl md:text-4xl lg:text-5xl font-semibold text-white leading-tight reveal delay-200">
                    ¿Estás liderando con visión la escalabilidad de tu empresa?
                </h2>
                <p class="mt-8 font-serif text-xl text-white/60 max-w-2xl mx-auto reveal delay-300">
                    Transforma los desafíos del crecimiento en oportunidades de excelencia operativa.
                </p>
                <div class="mt-12 reveal delay-400">
                    <a href="#contacto" class="inline-flex items-center gap-4 bg-white text-ethos-800 font-semibold py-4 px-12 rounded-xl text-lg tracking-wide hover:bg-ethos-50 hover:shadow-2xl hover:shadow-white/10 transition-all duration-500 hover:-translate-y-1">
                        Agenda una consulta gratuita
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </section>
    </section>
 
    <!-- Contact Section -->
    <section id="contacto" class="py-28 lg:py-36 bg-marble-50 relative overflow-hidden">
        <div class="absolute inset-0 meander-complex opacity-20"></div>
        <div class="decorative-circle w-[600px] h-[600px] -top-64 -right-64"></div>
 
        <div class="relative max-w-7xl mx-auto px-6 lg:px-8">
            <div class="grid lg:grid-cols-2 gap-20 lg:gap-32">
                <!-- Contact Info -->
                <div>
                    <div class="inline-flex items-center gap-4 mb-8 reveal-left">
                        <div class="w-16 h-px bg-gradient-to-r from-ethos-300 to-transparent"></div>
                        <span class="font-display text-sm tracking-[0.4em] text-ethos-600 uppercase">Contacto</span>
                    </div>
 
                    <h2 class="font-display text-4xl md:text-5xl lg:text-6xl font-semibold text-ethos-900 leading-tight reveal-left delay-200">
                        Comienza tu transformación organizacional
                    </h2>
 
                    <p class="mt-10 font-serif text-xl text-gray-600 leading-relaxed reveal-left delay-300">
                        Cuéntanos sobre los desafíos de tu empresa. Nuestro equipo de expertos te contactará para una evaluación inicial.
                    </p>
 
                    <!-- Contact Details -->
                    <div class="mt-14 space-y-8">
                        <div class="flex items-start gap-6 reveal-left delay-400" data-cursor-hover>
                            <div class="w-16 h-16 glass-premium rounded-xl flex items-center justify-center flex-shrink-0 shadow-lg">
                                <svg class="w-7 h-7 text-ethos-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </div>
                            <div class="pt-4">
                                <h4 class="font-display font-semibold text-ethos-900 text-lg">Ubicación</h4>
                                <p class="text-gray-600 font-serif mt-1">Caracas, Venezuela</p>
                            </div>
                        </div>
 
                        <div class="flex items-start gap-6 reveal-left delay-500" data-cursor-hover>
                            <div class="w-16 h-16 glass-premium rounded-xl flex items-center justify-center flex-shrink-0 shadow-lg">
                                <svg class="w-7 h-7 text-ethos-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <div class="pt-4">
                                <h4 class="font-display font-semibold text-ethos-900 text-lg">Email</h4>
                                <a href="mailto:ethossummitgroup@gmail.com" class="text-ethos-600 hover:text-ethos-700 transition-colors font-serif mt-1 block">
                                    ethossummitgroup@gmail.com
                                </a>
                            </div>
                        </div>
 
                        <div class="flex items-start gap-6 reveal-left delay-600" data-cursor-hover>
                            <div class="w-16 h-16 glass-premium rounded-xl flex items-center justify-center flex-shrink-0 shadow-lg">
                                <svg class="w-7 h-7 text-ethos-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                                </svg>
                            </div>
                            <div class="pt-4">
                                <h4 class="font-display font-semibold text-ethos-900 text-lg">Instagram</h4>
                                <a href="https://instagram.com/ethosconsultoria_" target="_blank" class="text-ethos-600 hover:text-ethos-700 transition-colors font-serif mt-1 block">
                                    @ethosconsultoria_
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
 
                <!-- Contact Form -->
                <div class="relative reveal-right">
                    <div class="decorative-circle w-[300px] h-[300px] -top-16 -right-16"></div>
 
                    <div class="relative glass-premium rounded-3xl p-8 lg:p-12 shadow-2xl">
                        <h3 class="font-display text-2xl font-semibold text-ethos-900 mb-10 pb-6 border-b border-marble-200">
                            Envíanos un mensaje
                        </h3>
 
                        <form action="#" method="POST" class="space-y-6">
                            <div class="grid md:grid-cols-2 gap-6">
                                <div>
                                    <label for="nombre" class="block text-sm font-medium text-gray-700 mb-3">Nombre</label>
                                    <input type="text" id="nombre" name="nombre" required
                                           class="input-premium w-full px-5 py-4 rounded-xl outline-none"
                                           placeholder="Tu nombre">
                                </div>
                                <div>
                                    <label for="apellido" class="block text-sm font-medium text-gray-700 mb-3">Apellido</label>
                                    <input type="text" id="apellido" name="apellido" required
                                           class="input-premium w-full px-5 py-4 rounded-xl outline-none"
                                           placeholder="Tu apellido">
                                </div>
                            </div>
 
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-3">Email</label>
                                <input type="email" id="email" name="email" required
                                       class="input-premium w-full px-5 py-4 rounded-xl outline-none"
                                       placeholder="tu@email.com">
                            </div>
 
                            <div>
                                <label for="sector" class="block text-sm font-medium text-gray-700 mb-3">Sector / Rubro</label>
                                <select id="sector" name="sector" required
                                        class="input-premium w-full px-5 py-4 rounded-xl outline-none cursor-pointer">
                                    <option value="">Selecciona tu sector</option>
                                    <option value="comercio">Comercio</option>
                                    <option value="industria">Industria</option>
                                    <option value="servicios">Servicios</option>
                                    <option value="tecnologia">Tecnología</option>
                                    <option value="salud">Salud</option>
                                    <option value="finanzas">Finanzas</option>
                                    <option value="educacion">Educación</option>
                                    <option value="otro">Otro</option>
                                </select>
                            </div>
 
                            <div>
                                <label for="mensaje" class="block text-sm font-medium text-gray-700 mb-3">Mensaje</label>
                                <textarea id="mensaje" name="mensaje" rows="5" required
                                          class="input-premium w-full px-5 py-4 rounded-xl outline-none resize-none"
                                          placeholder="Cuéntanos sobre los desafíos de tu empresa..."></textarea>
                            </div>
 
                            <button type="submit" 
                                    class="btn-premium w-full text-white font-semibold py-5 px-8 rounded-xl flex items-center justify-center gap-3 text-lg">
                                Enviar mensaje
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
 
    <!-- Footer -->
    <footer class="bg-ethos-950 text-white py-20 relative overflow-hidden">
        <div class="absolute inset-0 meander-complex opacity-10"></div>
        <div class="absolute top-0 left-0 right-0 h-px bg-gradient-to-r from-transparent via-ethos-400/20 to-transparent"></div>
 
        <div class="relative max-w-7xl mx-auto px-6 lg:px-8">
            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-12 lg:gap-16 mb-16">
                <!-- Brand -->
                <div class="lg:col-span-2">
                    <div class="flex items-center gap-4 mb-8">
                        <div class="relative w-14 h-14 bg-white rounded-xl flex items-center justify-center shadow-lg">
                            <span class="font-display font-bold text-ethos-700 text-3xl">Ε</span>
                        </div>
                        <div>
                            <span class="font-display text-2xl font-semibold tracking-[0.15em]">ETHOS</span>
                            <span class="block text-xs text-white/40 tracking-[0.4em] uppercase mt-1">Consultoría Estratégica</span>
                        </div>
                    </div>
 
                    <p class="font-serif text-white/60 leading-relaxed max-w-md text-lg">
                        Diseñamos procesos inteligentes, auditoría estratégica y sistemas empresariales sólidos que te permiten operar con claridad.
                    </p>
 
                    <p class="mt-8 font-serif text-white/30 italic">
                        "ἦθος ἀνὴρ δαίμων" — El carácter es destino
                    </p>
                </div>
 
                <!-- Links -->
                <div>
                    <h4 class="font-display font-semibold mb-8 text-lg">Navegación</h4>
                    <ul class="space-y-4">
                        <li><a href="#problemas" class="text-white/60 hover:text-white transition-colors font-serif">Problemas</a></li>
                        <li><a href="#metodologia" class="text-white/60 hover:text-white transition-colors font-serif">Metodología</a></li>
                        <li><a href="#servicios" class="text-white/60 hover:text-white transition-colors font-serif">Servicios</a></li>
                        <li><a href="#contacto" class="text-white/60 hover:text-white transition-colors font-serif">Contacto</a></li>
                    </ul>
                </div>
 
                <!-- Social -->
                <div>
                    <h4 class="font-display font-semibold mb-8 text-lg">Síguenos</h4>
                    <div class="flex gap-4">
                        <a href="https://instagram.com/ethosconsultoria_" target="_blank" 
                           class="w-14 h-14 bg-white/10 hover:bg-white/20 rounded-xl flex items-center justify-center transition-all duration-300 hover:scale-110"
                           data-cursor-hover>
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2.163c3.589 0 4.025.012 5.407.078 1.302.059 2.009.276 2.477.46.625.242 1.07.534 1.538 1.002.468.468.76.913 1.002 1.538.184.468.401 1.175.46 2.477.066 1.382.078 1.817.078 5.407s-.012 4.025-.078 5.407c-.059 1.302-.276 2.009-.46 2.477-.242.625-.534 1.07-1.002 1.538-.468.468-.913.76-1.538 1.002-.468.184-1.175.401-2.477.46-1.382.066-1.817.078-5.407.078s-4.025-.012-5.407-.078c-1.302-.059-2.009-.276-2.477-.46-.625-.242-1.07-.534-1.538-1.002-.468-.468-.76-.913-1.002-1.538-.184-.468-.401-1.175-.46-2.477-.066-1.382-.078-1.817-.078-5.407s.012-4.025.078-5.5c.059-1.4.276-2.355.612-3.19.336-.864.787-1.597 1.52-2.33.733-.733 1.466-1.184 2.33-1.52.835-.326 1.79-.552 3.19-.612 1.398-.066 1.855-.081 5.5-.081zm0-2.163c-3.645 0-4.102.015-5.5.081-1.4.06-2.355.286-3.19.612-.864.336-1.597.787-2.33 1.52-.733.733-1.184 1.466-1.52 2.33-.326.835-.552 1.79-.612 3.19-.066 1.398-.081 1.855-.081 5.5s.015 4.102.081 5.5c.06 1.4.286 2.355.612 3.19.336.864.787 1.597 1.52 2.33.733.733 1.466 1.184 2.33 1.52.835.326 1.79.552 3.19.612 1.398.066 1.855.081 5.5.081s4.102-.015 5.5-.081c1.4-.06 2.355-.286 3.19-.612.864-.336 1.597-.787 2.33-1.52.733-.733 1.184-1.466 1.52-2.33.326-.835.552-1.79.612-3.19.066-1.398.081-1.855.081-5.5s-.015-4.102-.081-5.5c-.06-1.4-.286-2.355-.612-3.19-.336-.864-.787-1.597-1.52-2.33-.733-.733-1.466-1.184-2.33-1.52-.835-.326-1.79-.552-3.19-.612-1.398-.066-1.855-.081-5.5-.081zm0 5.838c-3.803 0-6.886 3.083-6.886 6.886s3.083 6.886 6.886 6.886 6.886-3.083 6.886-6.886-3.083-6.886-6.886-6.886zm0 11.351c-2.462 0-4.465-2.003-4.465-4.465s2.003-4.465 4.465-4.465 4.465 2.003 4.465 4.465-2.003 4.465-4.465 4.465zm8.753-11.601c0 .889-.72 1.609-1.609 1.609s-1.609-.72-1.609-1.609.72-1.609 1.609-1.609 1.609.72 1.609 1.609z"/>
                            </svg>
                        </a>
                        <a href="mailto:ethossummitgroup@gmail.com" 
                           class="w-14 h-14 bg-white/10 hover:bg-white/20 rounded-xl flex items-center justify-center transition-all duration-300 hover:scale-110"
                           data-cursor-hover>
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
 
            <!-- Bottom -->
            <div class="pt-10 border-t border-white/10 flex flex-col md:flex-row items-center justify-between gap-6">
                <p class="text-white/40 font-serif">
                    © {{ date('Y') }} ETHOS Consultoría. Todos los derechos reservados.
                </p>
                <p class="text-white/20 text-sm">
                    Caracas, Venezuela
                </p>
            </div>
        </div>
    </footer>

    <button id="assistant-fab" class="assistant-fab flex items-center justify-center" type="button" aria-label="Abrir asistente ETHOS" aria-controls="assistant-modal" aria-expanded="false">
        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 10h8M8 14h5m-1 7a9 9 0 10-9-9c0 1.7.48 3.29 1.31 4.64L3 21l4.6-1.31A8.96 8.96 0 0012 21z"/>
        </svg>
    </button>

    <section id="assistant-modal" class="assistant-modal assistant-hidden" role="dialog" aria-modal="true" aria-labelledby="assistant-title" aria-describedby="assistant-subtitle">
        <header class="assistant-header px-4 py-3 flex items-start justify-between gap-3">
            <div>
                <h3 id="assistant-title" class="font-display text-lg font-semibold leading-tight">Asistente ETHOS</h3>
                <p id="assistant-subtitle" class="text-xs text-white/80">Preguntas sobre servicios, horarios, compras y soporte</p>
            </div>
            <div class="flex items-center gap-2">
                <button id="assistant-minimize" type="button" class="w-8 h-8 rounded-lg bg-white/10 hover:bg-white/20" aria-label="Minimizar chat">−</button>
                <button id="assistant-close" type="button" class="w-8 h-8 rounded-lg bg-white/10 hover:bg-white/20" aria-label="Cerrar chat">×</button>
            </div>
        </header>

        <div id="assistant-panel" class="bg-white">
            <div id="assistant-messages" class="assistant-messages p-4 space-y-3" aria-live="polite"></div>

            <div class="assistant-suggestions px-4 pb-3 flex flex-wrap gap-2">
                <button type="button" data-assistant-question="¿Qué servicios ofrece ETHOS?" class="text-xs rounded-full px-3 py-1.5">Servicios</button>
                <button type="button" data-assistant-question="¿Cuál es el horario de atención?" class="text-xs rounded-full px-3 py-1.5">Horarios</button>
                <button type="button" data-assistant-question="¿Cómo es el proceso de compra?" class="text-xs rounded-full px-3 py-1.5">Compra</button>
                <button type="button" data-assistant-question="Necesito soporte técnico" class="text-xs rounded-full px-3 py-1.5">Soporte</button>
            </div>

            <div class="px-4 pb-3">
                <button id="assistant-clear" type="button" class="assistant-clear-btn w-full inline-flex items-center justify-center gap-2 text-sm rounded-xl px-3 py-2" aria-label="Limpiar chat" disabled>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 7h16M9 7V5a1 1 0 011-1h4a1 1 0 011 1v2m-8 0l1 12a1 1 0 001 .9h6a1 1 0 001-.9l1-12"/>
                    </svg>
                    <span>Limpiar chat</span>
                </button>
            </div>

            <form id="assistant-form" class="p-4 border-t border-gray-100 flex items-end gap-2">
                <label for="assistant-input" class="sr-only">Mensaje para el asistente</label>
                <textarea id="assistant-input" rows="1" maxlength="1200" class="input-premium w-full rounded-xl px-3 py-2 text-sm resize-none" placeholder="Escribe tu pregunta..."></textarea>
                <button id="assistant-send" type="submit" class="btn-premium text-white px-4 py-2 rounded-xl text-sm">Enviar</button>
            </form>
        </div>

        <div id="assistant-confirm" class="assistant-confirm-backdrop hidden" role="alertdialog" aria-modal="true" aria-labelledby="assistant-confirm-title" aria-describedby="assistant-confirm-description">
            <div class="assistant-confirm-modal p-4">
                <h4 id="assistant-confirm-title" class="font-display text-lg text-ethos-900 font-semibold">¿Limpiar conversación?</h4>
                <p id="assistant-confirm-description" class="mt-2 text-sm text-gray-600">Se eliminarán todos los mensajes del chat actual.</p>
                <div class="mt-4 flex gap-2 justify-end">
                    <button id="assistant-cancel-clear" type="button" class="text-sm px-3 py-2 rounded-lg border border-gray-200 text-gray-700 hover:bg-gray-50">Cancelar</button>
                    <button id="assistant-confirm-clear" type="button" class="text-sm px-3 py-2 rounded-lg bg-red-600 text-white hover:bg-red-700">Sí, limpiar</button>
                </div>
            </div>
        </div>
    </section>
 
    <!-- JavaScript -->
    <script>
        // Custom Cursor
        const cursor = document.getElementById('cursor');
        let mouseX = 0, mouseY = 0;
        let cursorX = 0, cursorY = 0;
 
        document.addEventListener('mousemove', (e) => {
            mouseX = e.clientX;
            mouseY = e.clientY;
        });
 
        function animateCursor() {
            cursorX += (mouseX - cursorX) * 0.12;
            cursorY += (mouseY - cursorY) * 0.12;
            cursor.style.left = cursorX - 6 + 'px';
            cursor.style.top = cursorY - 6 + 'px';
            requestAnimationFrame(animateCursor);
        }
        animateCursor();
 
        // Cursor hover effect
        document.querySelectorAll('[data-cursor-hover]').forEach(el => {
            el.addEventListener('mouseenter', () => cursor.classList.add('hover'));
            el.addEventListener('mouseleave', () => cursor.classList.remove('hover'));
        });
 
        // Hide cursor on mobile
        if ('ontouchstart' in window) {
            cursor.style.display = 'none';
        }
 
        // Navbar scroll effect
        const navbar = document.getElementById('navbar');
        const navBg = document.getElementById('nav-bg');
 
        window.addEventListener('scroll', () => {
            if (window.scrollY > 100) {
                navBg.classList.add('opacity-100');
                navBg.classList.remove('opacity-0');
            } else {
                navBg.classList.remove('opacity-100');
                navBg.classList.add('opacity-0');
            }
        });
 
        // Mobile menu
        const mobileMenuBtn = document.getElementById('mobile-menu-btn');
        const mobileMenu = document.getElementById('mobile-menu');
 
        mobileMenuBtn.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
        });
 
        mobileMenu.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', () => {
                mobileMenu.classList.add('hidden');
            });
        });
 
        // Smooth scroll
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
 
        // Scroll Reveal Animation
        const revealElements = document.querySelectorAll('.reveal, .reveal-left, .reveal-right, .reveal-scale');
 
        const revealObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('active');
                }
            });
        }, {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        });
 
        revealElements.forEach(el => revealObserver.observe(el));
 
        // Service Card Mouse Tracking
        document.querySelectorAll('.service-card-premium').forEach(card => {
            card.addEventListener('mousemove', (e) => {
                const rect = card.getBoundingClientRect();
                const x = ((e.clientX - rect.left) / rect.width) * 100;
                const y = ((e.clientY - rect.top) / rect.height) * 100;
                card.style.setProperty('--mouse-x', x + '%');
                card.style.setProperty('--mouse-y', y + '%');
            });
        });
 
        // Parallax effect on hero
        window.addEventListener('scroll', () => {
            const scrolled = window.pageYOffset;
            const hero = document.querySelector('section');
            if (hero && scrolled < window.innerHeight) {
                const decorativeCircles = hero.querySelectorAll('.decorative-circle');
                decorativeCircles.forEach((circle, i) => {
                    const speed = 0.1 + (i * 0.05);
                    circle.style.transform = `translateY(${scrolled * speed}px)`;
                });
            }
        });

        const assistantState = {
            isOpen: false,
            isMinimized: false,
            loading: false,
            history: [],
            sessionKey: 'ethos_ai_chat_history',
        };

        const assistantRefs = {
            fab: document.getElementById('assistant-fab'),
            modal: document.getElementById('assistant-modal'),
            panel: document.getElementById('assistant-panel'),
            close: document.getElementById('assistant-close'),
            minimize: document.getElementById('assistant-minimize'),
            clear: document.getElementById('assistant-clear'),
            confirm: document.getElementById('assistant-confirm'),
            confirmClear: document.getElementById('assistant-confirm-clear'),
            cancelClear: document.getElementById('assistant-cancel-clear'),
            form: document.getElementById('assistant-form'),
            input: document.getElementById('assistant-input'),
            messages: document.getElementById('assistant-messages'),
            suggestions: document.querySelectorAll('[data-assistant-question]'),
        };

        const escapeHtml = (value) => {
            return String(value).replace(/[&<>"']/g, (char) => ({
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#39;'
            }[char]));
        };

        const renderAssistantHistory = () => {
            assistantRefs.messages.innerHTML = assistantState.history.map((item) => {
                const side = item.role === 'user' ? 'justify-end' : 'justify-start';
                const bubble = item.role === 'user' ? 'assistant-bubble-user' : 'assistant-bubble-ai';
                return `<div class="flex ${side}"><div class="max-w-[85%] rounded-2xl px-3 py-2 text-sm leading-relaxed ${bubble}">${escapeHtml(item.content)}</div></div>`;
            }).join('');

            if (assistantState.loading) {
                assistantRefs.messages.insertAdjacentHTML(
                    'beforeend',
                    '<div class="flex justify-start"><div class="assistant-bubble-ai rounded-2xl px-3 py-2"><div class="assistant-typing inline-flex gap-1"><span></span><span></span><span></span></div></div></div>'
                );
            }

            assistantRefs.messages.scrollTop = assistantRefs.messages.scrollHeight;
            sessionStorage.setItem(assistantState.sessionKey, JSON.stringify(assistantState.history.slice(-20)));
            setAssistantClearButtonState();
        };

        const setAssistantClearButtonState = () => {
            if (!assistantRefs.clear) {
                return;
            }
            const hasMessages = assistantState.history.length > 0;
            assistantRefs.clear.disabled = assistantState.loading || !hasMessages;
            assistantRefs.clear.setAttribute('aria-disabled', assistantRefs.clear.disabled ? 'true' : 'false');
        };

        const setAssistantOpen = (value) => {
            assistantState.isOpen = value;
            assistantRefs.modal.classList.toggle('assistant-hidden', !value);
            assistantRefs.fab.setAttribute('aria-expanded', value ? 'true' : 'false');
            if (value && !assistantState.isMinimized) {
                setTimeout(() => assistantRefs.input.focus(), 120);
            }
        };

        const setAssistantMinimized = (value) => {
            assistantState.isMinimized = value;
            assistantRefs.panel.classList.toggle('hidden', value);
            assistantRefs.minimize.textContent = value ? '+' : '−';
            assistantRefs.minimize.setAttribute('aria-label', value ? 'Expandir chat' : 'Minimizar chat');
        };

        const setAssistantClearConfirmOpen = (value) => {
            if (!assistantRefs.confirm) {
                return;
            }
            assistantRefs.confirm.classList.toggle('hidden', !value);
            if (value) {
                setTimeout(() => assistantRefs.confirmClear?.focus(), 60);
            } else {
                setTimeout(() => assistantRefs.clear?.focus(), 60);
            }
        };

        const clearAssistantHistory = async () => {
            if (assistantState.loading) {
                return;
            }
            const clearedCount = assistantState.history.length;
            assistantState.history = [];
            sessionStorage.removeItem(assistantState.sessionKey);
            renderAssistantHistory();
            setAssistantClearConfirmOpen(false);
            try {
                await fetch('/assistant/chat/clear', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        cleared_count: clearedCount
                    }),
                });
            } catch (_) {}
        };

        const sendAssistantMessage = async (message) => {
            const content = String(message || '').trim();
            if (!content || assistantState.loading) {
                return;
            }

            assistantState.history.push({ role: 'user', content });
            assistantState.loading = true;
            renderAssistantHistory();
            assistantRefs.input.value = '';
            assistantRefs.input.style.height = 'auto';

            try {
                const response = await fetch('/assistant/chat', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        message: content,
                        history: assistantState.history.slice(-10),
                    }),
                });

                const payload = await response.json();

                if (!response.ok || !payload.reply) {
                    throw new Error(payload.message || 'No fue posible obtener respuesta.');
                }

                assistantState.history.push({ role: 'assistant', content: payload.reply });
            } catch (error) {
                assistantState.history.push({
                    role: 'assistant',
                    content: 'No pude conectarme en este momento. Intenta nuevamente o contáctanos por correo para atención inmediata.'
                });
            } finally {
                assistantState.loading = false;
                renderAssistantHistory();
            }
        };

        if (
            assistantRefs.fab &&
            assistantRefs.modal &&
            assistantRefs.panel &&
            assistantRefs.close &&
            assistantRefs.minimize &&
            assistantRefs.clear &&
            assistantRefs.confirm &&
            assistantRefs.confirmClear &&
            assistantRefs.cancelClear &&
            assistantRefs.form &&
            assistantRefs.input &&
            assistantRefs.messages
        ) {
            const storedHistory = sessionStorage.getItem(assistantState.sessionKey);

            if (storedHistory) {
                try {
                    const parsed = JSON.parse(storedHistory);
                    if (Array.isArray(parsed)) {
                        assistantState.history = parsed.slice(-20);
                    }
                } catch (_) {}
            }

            if (assistantState.history.length === 0) {
                assistantState.history.push({
                    role: 'assistant',
                    content: 'Hola. Soy el asistente ETHOS. Te ayudo con información de servicios,horarios, procesos de compra y soporte técnico.'
                });
            }

            renderAssistantHistory();

            assistantRefs.fab.addEventListener('click', () => {
                setAssistantOpen(!assistantState.isOpen);
                if (assistantState.isMinimized) {
                    setAssistantMinimized(false);
                }
            });

            assistantRefs.close.addEventListener('click', () => {
                setAssistantOpen(false);
            });

            assistantRefs.minimize.addEventListener('click', () => {
                setAssistantMinimized(!assistantState.isMinimized);
            });

            assistantRefs.clear.addEventListener('click', () => {
                setAssistantClearConfirmOpen(true);
            });

            assistantRefs.cancelClear.addEventListener('click', () => {
                setAssistantClearConfirmOpen(false);
            });

            assistantRefs.confirmClear.addEventListener('click', () => {
                clearAssistantHistory();
            });

            assistantRefs.form.addEventListener('submit', (event) => {
                event.preventDefault();
                sendAssistantMessage(assistantRefs.input.value);
            });

            assistantRefs.input.addEventListener('keydown', (event) => {
                if (event.key === 'Enter' && !event.shiftKey) {
                    event.preventDefault();
                    sendAssistantMessage(assistantRefs.input.value);
                }
            });

            assistantRefs.input.addEventListener('input', () => {
                assistantRefs.input.style.height = 'auto';
                assistantRefs.input.style.height = `${Math.min(assistantRefs.input.scrollHeight, 132)}px`;
            });

            assistantRefs.suggestions.forEach((button) => {
                button.addEventListener('click', () => {
                    setAssistantOpen(true);
                    if (assistantState.isMinimized) {
                        setAssistantMinimized(false);
                    }
                    sendAssistantMessage(button.dataset.assistantQuestion);
                });
            });

            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape' && !assistantRefs.confirm.classList.contains('hidden')) {
                    setAssistantClearConfirmOpen(false);
                    return;
                }
                if (event.key === 'Escape' && assistantState.isOpen) {
                    setAssistantOpen(false);
                }
            });
        }
    </script>
</body>
</html>
