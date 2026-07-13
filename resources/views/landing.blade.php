<!DOCTYPE html>
<html lang="es" class="dark">
<head>
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/logowids.svg') }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WIDS | Web Intrusion Detection System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/flowbite@1.4.1/dist/flowbite.js"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: { "50": "#fffbeb", "100": "#fef3c7", "200": "#fde68a", "300": "#fcd34d", "400": "#fbbf24", "500": "#f59e0b", "600": "#d97706", "700": "#b45309", "800": "#92400e", "900": "#78350f" }
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-[#0f0f0f] text-gray-100">

    <header class="fixed w-full z-50">
        
        <nav class="bg-[#0f0f0f] border-b border-gray-800 py-2.5">
            <div class="flex flex-wrap items-center justify-between max-w-screen-xl px-4 mx-auto">
        <a href="/" class="flex items-center">
    <img src="{{ asset('images/logowids.svg') }}" class="h-8 mr-3" alt="WIDS Logo" />
    
    <span class="self-center text-xl font-semibold whitespace-nowrap text-white">WIDS Engine</span>
        </a>
<div class="flex items-center lg:order-2">
    <a href="/horizon" class="flex items-center text-white bg-amber-600 hover:bg-amber-500 font-medium rounded-lg text-sm px-5 py-2.5 transition-all">
        Horizon
        <svg class="w-5 h-5 ml-2" viewBox="0 0 30 30" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
            <path d="M5.26176342 26.4094389C2.04147988 23.6582233 0 19.5675182 0 15c0-4.1421356 1.67893219-7.89213562 4.39339828-10.60660172C7.10786438 1.67893219 10.8578644 0 15 0c8.2842712 0 15 6.71572875 15 15 0 8.2842712-6.7157288 15-15 15-3.716753 0-7.11777662-1.3517984-9.73823658-3.5905611zM4.03811305 15.9222506C5.70084247 14.4569342 6.87195416 12.5 10 12.5c5 0 5 5 10 5 3.1280454 0 4.2991572-1.9569336 5.961887-3.4222502C25.4934253 8.43417206 20.7645408 4 15 4 8.92486775 4 4 8.92486775 4 15c0 .3105915.01287248.6181765.03811305.9222506z"/>
        </svg>
    </a>
</div>
            </div>
        </nav>
    </header>

    <!-- Hero Section -->
<section class="relative overflow-hidden min-h-[550px]">
        
        <style>
            @keyframes marioFloat {
                0% {
                    filter: drop-shadow(0 5px 15px rgba(0,0,0,0.6));
                    transform: translateY(0px) rotate(var(--rot));
                }
                50% {
                    filter: drop-shadow(0 25px 15px rgba(0,0,0,0.2));
                    transform: translateY(-20px) rotate(var(--rot));
                }
                100% {
                    filter: drop-shadow(0 5px 15px rgba(0,0,0,0.6));
                    transform: translateY(0px) rotate(var(--rot));
                }
            }

            .wids-mario-float-1 { animation: marioFloat 6s ease-in-out infinite; }
            .wids-mario-float-2 { animation: marioFloat 6s ease-in-out infinite; animation-delay: -2s; }
            .wids-mario-float-3 { animation: marioFloat 6s ease-in-out infinite; animation-delay: -4s; }
        </style>
        
        <div class="absolute inset-0 opacity-[0.03] pointer-events-none select-none z-0 grid grid-cols-4 sm:grid-cols-6 lg:grid-cols-8 gap-4 justify-items-center content-center overflow-hidden brightness-0 invert">
            @for ($i = 0; $i < 64; $i++)
                @php
                    $rotations = ['0deg', '12deg', '45deg', '90deg', '-12deg', '-45deg', '30deg', '60deg'];
                    $currentRot = $rotations[$i % count($rotations)];
                    $animationClass = 'wids-mario-float-' . (($i % 3) + 1);
                @endphp
                <img src="{{ asset('images/logowids.svg') }}" 
                     class="w-[180px] h-[180px] object-contain {{ $animationClass }}" 
                     style="--rot: {{ $currentRot }};"
                     alt="WIDS Pattern">
            @endfor
        </div>

        <div class="relative grid max-w-screen-xl px-4 pt-20 pb-8 mx-auto lg:gap-8 xl:gap-0 lg:py-16 lg:grid-cols-12 lg:pt-28 z-10">
            <div class="mr-auto place-self-center lg:col-span-8">
                <h1 class="max-w-2xl mb-4 text-4xl font-extrabold leading-none tracking-tight md:text-5xl xl:text-6xl text-white">
                    WIDS: Detección de intrusiones en tiempo real.
                </h1>
                <p class="max-w-2xl mb-6 font-light text-gray-400 lg:mb-8 md:text-lg lg:text-xl leading-relaxed">
                    Sistema de seguridad académica diseñado para la monitorización de ataques, análisis de tráfico y respuesta automatizada.
                </p>
                <div class="space-y-4 sm:flex sm:space-y-0 sm:space-x-4">
                    <a href="/admin" class="inline-flex items-center justify-center w-full px-5 py-3 text-sm font-medium text-center text-white bg-amber-600 rounded-lg sm:w-auto hover:bg-amber-500 transition-all shadow-[0_4px_20px_rgba(217,119,6,0.15)]">
                        Panel de Administración
                    </a>
                </div>
            </div>
            
            <div class="hidden lg:mt-0 lg:col-span-4 lg:flex"></div>   
        </div>
    </section>
    <!-- Attack Lab Section -->
<section class="bg-[#1a1a1a] py-16">
        <div class="max-w-screen-xl px-4 mx-auto">
            <h2 class="mb-12 text-3xl font-extrabold tracking-tight text-center text-amber-500 lg:text-3xl">Attack Lab (Centro de Pruebas)</h2>
            
            <div class="space-y-8 sm:grid sm:grid-cols-2 lg:grid-cols-4 sm:gap-6 xl:gap-8 sm:space-y-0">
                
                <div class="flex flex-col p-6 mx-auto w-full text-center bg-[#1f1f1f] border border-gray-800 rounded-lg shadow xl:p-8">
                    <h3 class="mb-4 text-2xl font-semibold text-white">Brute Force / Auth</h3>
                    <p class="font-light text-gray-400 sm:text-lg mb-6">Prueba de fuerza bruta y validación de OTP.</p>
                    <a href="/login" class="text-white bg-amber-600 hover:bg-amber-500 font-medium rounded-lg text-sm px-5 py-2.5 mt-auto transition-colors">Acceder al Login</a>
                </div>

                <div class="flex flex-col p-6 mx-auto w-full text-center bg-[#1f1f1f] border border-gray-800 rounded-lg shadow xl:p-8">
                    <h3 class="mb-4 text-2xl font-semibold text-white">XSS Payload</h3>
                    <p class="font-light text-gray-400 sm:text-lg mb-6">Inyección de scripts maliciosos para comprobar filtrado.</p>
                    <a href="/xssattacker" class="text-white bg-amber-600 hover:bg-amber-500 font-medium rounded-lg text-sm px-5 py-2.5 mt-auto transition-colors">Acceder al Ataque</a>
                </div>

                <div class="flex flex-col p-6 mx-auto w-full text-center bg-[#1f1f1f] border border-gray-800 rounded-lg shadow xl:p-8">
                    <h3 class="mb-4 text-2xl font-semibold text-white">SQLi Test</h3>
                    <p class="font-light text-gray-400 sm:text-lg mb-6">Prueba para inyección de SQL.</p>
                    <a href="/test" class="text-white bg-amber-600 hover:bg-amber-500 font-medium rounded-lg text-sm px-5 py-2.5 mt-auto transition-colors">Acceder al Test</a>
                </div>

                <div class="flex flex-col p-6 mx-auto w-full text-center bg-[#1f1f1f] border border-red-900/50 rounded-lg shadow xl:p-8 relative overflow-hidden">
                    <div class="absolute top-0 right-0 px-2 py-1 text-xs font-bold text-white bg-red-600 rounded-bl-lg">Puerto 8001</div>
                    
                    <h3 class="mb-4 text-2xl font-semibold text-white">CSRF Attack</h3>
                    <p class="font-light text-gray-400 sm:text-lg mb-6">Simulación de origen cruzado alojada en un servidor externo.</p>
                    
                    <a href="/launch-csrf-trap" target="_blank" class="text-white bg-red-700 hover:bg-red-600 font-medium rounded-lg text-sm px-5 py-2.5 mt-auto transition-colors">Abrir Web Trampa</a>
                </div>

            </div>
        </div>
    </section>

</body>
</html>