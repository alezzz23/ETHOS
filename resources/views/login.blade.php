<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ETHOS | Login</title>
    <meta name="description" content="Acceso interno — ETHOS Consultoría Estratégica.">

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
                    },
                    fontFamily: {
                        'display': ['Cinzel', 'serif'],
                        'serif': ['Cormorant Garamond', 'serif'],
                        'body': ['Outfit', 'sans-serif'],
                    },
                }
            }
        }
    </script>

    <style>
        :root {
            --transition-smooth: cubic-bezier(0.4, 0, 0.2, 1);
            --transition-expo: cubic-bezier(0.16, 1, 0.3, 1);
        }

        * { box-sizing: border-box; }
        html { scroll-behavior: smooth; }

        .noise-overlay {
            position: fixed;
            inset: 0;
            pointer-events: none;
            z-index: 50;
            opacity: 0.03;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noise'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noise)'/%3E%3C/svg%3E");
        }

        .meander-complex {
            background-image: radial-gradient(circle, rgba(0, 102, 204, 0.06) 1px, transparent 1px);
            background-size: 32px 32px;
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

        .input-premium {
            background: linear-gradient(135deg,
                rgba(255, 255, 255, 1) 0%,
                rgba(248, 246, 243, 1) 100%
            );
            border: 2px solid #efebe6;
            transition: all 0.35s var(--transition-expo);
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

        .decorative-circle {
            position: absolute;
            border-radius: 50%;
            background: linear-gradient(135deg,
                rgba(0, 102, 204, 0.15) 0%,
                rgba(54, 163, 255, 0.12) 50%,
                rgba(0, 102, 204, 0.08) 100%
            );
            filter: blur(60px);
        }

        @keyframes floatUp {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            25% { transform: translateY(-20px) rotate(1deg); }
            75% { transform: translateY(-10px) rotate(-1deg); }
        }

        .animate-float { animation: floatUp 12s ease-in-out infinite; }
    </style>
</head>

<body class="font-body antialiased bg-gradient-to-br from-ethos-950 via-ethos-900 to-ethos-800 text-white min-h-screen overflow-x-hidden">
    <div class="noise-overlay"></div>

    <main class="relative min-h-screen">
        <div class="absolute inset-0 meander-complex opacity-20"></div>
        <div class="decorative-circle w-[520px] h-[520px] -top-56 -right-56 animate-float"></div>
        <div class="decorative-circle w-[420px] h-[420px] -bottom-56 -left-56 animate-float" style="animation-delay: 4s;"></div>

        <div class="relative z-10 max-w-7xl mx-auto px-6 lg:px-8 py-10 min-h-screen flex flex-col">
            <div class="flex items-center justify-between">
                <a href="/" class="group inline-flex items-center gap-4">
                    <div class="relative w-12 h-12">
                        <div class="absolute inset-0 bg-white rounded-xl shadow-xl group-hover:shadow-2xl transition-all duration-500"></div>
                        <div class="absolute inset-0 flex items-center justify-center">
                            <span class="font-display text-2xl font-bold text-ethos-700 group-hover:scale-110 transition-transform duration-500">Ε</span>
                        </div>
                        <div class="absolute -bottom-1 left-1/2 -translate-x-1/2 w-10 h-1.5 bg-gradient-to-r from-ethos-600 via-ethos-400 to-ethos-600 rounded-full opacity-80 group-hover:w-12 transition-all duration-500"></div>
                    </div>
                    <div class="flex flex-col">
                        <span class="font-display text-xl font-semibold tracking-[0.15em] group-hover:tracking-[0.2em] transition-all duration-500">ETHOS</span>
                        <span class="text-[10px] text-white/50 tracking-[0.4em] uppercase">Acceso</span>
                    </div>
                </a>

                <a href="/" class="text-white/60 hover:text-white text-xs tracking-[0.25em] uppercase transition-colors duration-300">Volver</a>
            </div>

            <div class="mt-10 flex-1 flex items-center justify-center">
                <section class="relative w-full max-w-xl overflow-hidden rounded-3xl bg-white/95 text-ethos-950 shadow-2xl">
                    <div class="absolute inset-0 bg-gradient-to-br from-white via-marble-50 to-white"></div>
                    <div class="absolute inset-0 meander-complex opacity-[0.12]"></div>
                    <div class="absolute top-0 left-0 right-0 h-px bg-gradient-to-r from-transparent via-ethos-400/35 to-transparent"></div>
                    <div class="absolute bottom-0 left-0 right-0 h-px bg-gradient-to-r from-transparent via-ethos-400/20 to-transparent"></div>
                    <div class="relative p-10 lg:p-14">
                        <div class="flex items-start justify-between gap-6">
                            <div>
                                <h2 class="font-display text-2xl md:text-3xl font-semibold">Login</h2>
                                <p class="mt-2 text-sm text-ethos-900/60">Introduce tu correo y contraseña.</p>
                            </div>
                            <div class="hidden sm:flex items-center gap-2 rounded-full border border-ethos-200 bg-white px-4 py-2 text-[10px] tracking-[0.3em] uppercase text-ethos-900/60">
                                <span class="w-2 h-2 rounded-full bg-ethos-600"></span>
                                ETHOS
                            </div>
                        </div>

                        <form class="mt-10 space-y-6" method="post" action="/login" autocomplete="on">
                            @csrf

                            <div>
                                <label for="email" class="block text-xs tracking-[0.25em] uppercase text-ethos-900/60">Email</label>
                                <div class="mt-3">
                                    <input id="email" name="email" type="email" inputmode="email" autocomplete="email" required class="input-premium w-full rounded-2xl px-5 py-4 text-base text-ethos-950 placeholder:text-ethos-900/35 outline-none" placeholder="nombre@gmail.com">
                                </div>
                            </div>

                            <div>
                                <label for="password" class="block text-xs tracking-[0.25em] uppercase text-ethos-900/60">Contraseña</label>
                                <div class="mt-3 relative">
                                    <input id="password" name="password" type="password" autocomplete="current-password" required class="input-premium w-full rounded-2xl px-5 py-4 pr-14 text-base text-ethos-950 placeholder:text-ethos-900/35 outline-none" placeholder="••••••••••">
                                    <button type="button" id="togglePassword" class="absolute right-3 top-1/2 -translate-y-1/2 rounded-xl p-2 text-ethos-900/50 hover:text-ethos-900 transition-colors" aria-label="Mostrar u ocultar contraseña">
                                        <svg id="eyeOpen" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        <svg id="eyeClosed" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3l18 18" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.477 10.48a3 3 0 004.243 4.243" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.88 5.09A9.953 9.953 0 0112 5c4.477 0 8.268 2.943 9.542 7a9.96 9.96 0 01-4.257 5.07" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6.228 6.227A9.957 9.957 0 002.458 12c1.274 4.057 5.065 7 9.542 7 1.46 0 2.86-.313 4.132-.88" />
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <div class="flex items-center justify-between gap-6">
                                <label class="inline-flex items-center gap-3 text-sm text-ethos-900/70">
                                    <input type="checkbox" name="remember" class="h-4 w-4 rounded border-ethos-300 text-ethos-700 focus:ring-ethos-600">
                                    Recordarme
                                </label>
                                <a href="#" class="text-sm text-ethos-700 hover:text-ethos-800 font-medium transition-colors">¿Olvidaste tu contraseña?</a>
                            </div>

                            <button type="submit" class="btn-premium w-full rounded-2xl py-4 px-6 text-white font-semibold tracking-[0.2em] uppercase">
                                Ingresar
                            </button>

                            <div class="pt-6 border-t border-ethos-200">
                                <div class="flex items-center justify-between gap-4">
                                    <span class="text-xs tracking-[0.25em] uppercase text-ethos-900/45">Soporte</span>
                                    <a href="mailto:soporte@ethos.com" class="text-sm text-ethos-900/70 hover:text-ethos-900 transition-colors">soporte@ethos.com</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </section>
            </div>
        </div>
    </main>

    <script>
        (() => {
            const btn = document.getElementById('togglePassword');
            const input = document.getElementById('password');
            const openIcon = document.getElementById('eyeOpen');
            const closedIcon = document.getElementById('eyeClosed');

            if (!btn || !input || !openIcon || !closedIcon) return;

            btn.addEventListener('click', () => {
                const isPassword = input.getAttribute('type') === 'password';
                input.setAttribute('type', isPassword ? 'text' : 'password');
                openIcon.classList.toggle('hidden', isPassword);
                closedIcon.classList.toggle('hidden', !isPassword);
            });
        })();
    </script>
</body>
</html>
