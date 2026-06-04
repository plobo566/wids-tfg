<!DOCTYPE html>
<html lang="es" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laboratorio XSS | WIDS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { theme: { extend: { colors: { amber: { 500: '#f59e0b', 600: '#d97706' } } } } }
    </script>
</head>
<body class="bg-[#0f0f0f] text-gray-200 min-h-screen py-12 px-6">

<div class="max-w-xl mx-auto bg-[#1f1f1f] p-8 rounded-xl shadow-2xl border border-gray-800">
    
    <h1 class="text-3xl font-extrabold text-white text-center mb-6">Laboratorio XSS</h1>
    <p class="text-gray-400 text-sm text-center mb-8">
        Simulador de vulnerabilidades. Inyecta payloads para probar la capacidad de detección de tu motor WIDS.
    </p>

    <div class="bg-[#0f0f0f] p-5 rounded-lg border-l-4 border-amber-600 mb-8">
        <strong class="text-amber-500 block mb-3 uppercase text-xs tracking-widest">Payloads de prueba:</strong>
        <ul class="text-gray-400 text-sm space-y-2 font-mono">
            <li>1. <code>&lt;script&gt;alert('Hacked')&lt;/script&gt;</code></li>
            <li>2. <code>&lt;img src=x onerror=alert(1)&gt;</code></li>
            <li>3. <code>&lt;svg onload=alert(1)&gt;</code></li>
            <li>4. <code>javascript:alert('XSS')</code></li>
        </ul>
    </div>

    <form action="/login" method="POST" class="space-y-6">
        
        {{ csrf_field() }}

        <div>
            <label for="nombre" class="block text-sm font-medium text-gray-300 mb-2">Nombre de usuario:</label>
            <input type="text" id="nombre" name="username" placeholder="Ej: <script>...</script>" 
                   class="w-full px-4 py-3 bg-[#121212] border border-gray-700 rounded-lg text-white focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none transition-all">
        </div>

        <div>
            <label for="bio" class="block text-sm font-medium text-gray-300 mb-2">Biografía:</label>
            <textarea id="bio" name="bio" placeholder="Ej: <img src=x onerror=...>" 
                      class="w-full px-4 py-3 bg-[#121212] border border-gray-700 rounded-lg text-white h-32 focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none transition-all resize-none"></textarea>
        </div>

        <div>
            <label for="web" class="block text-sm font-medium text-gray-300 mb-2">Página Web:</label>
            <input type="text" id="web" name="website" placeholder="Ej: javascript:..." 
                   class="w-full px-4 py-3 bg-[#121212] border border-gray-700 rounded-lg text-white focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none transition-all">
        </div>

        <button type="submit" 
                class="w-full bg-amber-600 hover:bg-amber-500 text-white font-bold py-3 rounded-lg transition-all transform hover:scale-[1.02] active:scale-95">
            Ejecutar Prueba de Inyección
        </button>
    </form>
</div>

</body>
</html>