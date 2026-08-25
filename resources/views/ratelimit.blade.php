<!DOCTYPE html>
<html lang="es" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laboratorio Rate Limit | WIDS</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { theme: { extend: { colors: { amber: { 500: '#f59e0b', 600: '#d97706' } } } } }
    </script>
</head>
<body class="bg-[#0f0f0f] text-gray-200 min-h-screen py-12 px-6">

<div class="max-w-xl mx-auto bg-[#1f1f1f] p-8 rounded-xl shadow-2xl border border-gray-800">
    
    <h1 class="text-3xl font-extrabold text-white text-center mb-6">Laboratorio Rate Limit</h1>
    <p class="text-gray-400 text-sm text-center mb-8">
        Simulador de inundación (Flooding). Dispara múltiples peticiones concurrentes para probar la detección de anomalías volumétricas de tu WIDS.
    </p>

    <div class="bg-[#0f0f0f] p-5 rounded-lg border-l-4 border-amber-600 mb-8">
        <strong class="text-amber-500 block mb-3 uppercase text-xs tracking-widest">Información de la prueba:</strong>
        <p class="text-gray-400 text-sm">
            Este script utiliza JavaScript (fetch) para enviar ráfagas de peticiones GET en segundo plano. Se añade un parámetro aleatorio a la URL para evitar que el navegador guarde la respuesta en caché.
        </p>
    </div>

    <form id="attackForm" class="space-y-6">
        
        <div>
            <label for="target" class="block text-sm font-medium text-gray-300 mb-2">Endpoint Objetivo:</label>
            <input type="text" id="target" value="/test" 
                   class="w-full px-4 py-3 bg-[#121212] border border-gray-700 rounded-lg text-white focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none transition-all">
        </div>

        <div>
            <label for="count" class="block text-sm font-medium text-gray-300 mb-2">Número de Peticiones:</label>
            <input type="number" id="count" value="200" min="10" max="1000"
                   class="w-full px-4 py-3 bg-[#121212] border border-gray-700 rounded-lg text-white focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none transition-all">
        </div>

        <div id="progressDiv" class="hidden space-y-2">
            <div class="flex justify-between text-xs text-amber-500 font-bold">
                <span>Progreso del Ataque</span>
                <span id="progressText">0 / 200</span>
            </div>
            <div class="w-full bg-gray-700 rounded-full h-2.5">
                <div id="progressBar" class="bg-amber-600 h-2.5 rounded-full" style="width: 0%"></div>
            </div>
        </div>

        <button type="submit" id="submitBtn"
                class="w-full bg-amber-600 hover:bg-amber-500 text-white font-bold py-3 rounded-lg transition-all transform hover:scale-[1.02] active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed">
            Ejecutar Prueba de Inundación
        </button>
    </form>
</div>

<script>
    document.getElementById('attackForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const targetUrl = document.getElementById('target').value;
        const requestCount = parseInt(document.getElementById('count').value);
        const btn = document.getElementById('submitBtn');
        const progressDiv = document.getElementById('progressDiv');
        const progressText = document.getElementById('progressText');
        const progressBar = document.getElementById('progressBar');

        //reset ui
        btn.disabled = true;
        btn.innerText = 'Atacando...';
        progressDiv.classList.remove('hidden');
        progressBar.style.width = '0%';
        
        let completed = 0;

        //peticiones get
        for(let i = 0; i < requestCount; i++) {
            //Math.random() evita que el navegador devuelva un 304 Not Modified de la caché local
            const bypassCacheUrl = targetUrl + (targetUrl.includes('?') ? '&' : '?') + 'bypass=' + Math.random();
            
            fetch(bypassCacheUrl, { method: 'GET', cache: 'no-store' })
                .then(() => updateProgress())
                .catch(() => updateProgress()); //si falla (429 Too Many Requests) tb suma al progreso
        }

        function updateProgress() {
            completed++;
            const percent = (completed / requestCount) * 100;
            
            progressBar.style.width = percent + '%';
            progressText.innerText = `${completed} / ${requestCount}`;

            if (completed === requestCount) {
                btn.innerText = 'Prueba Finalizada';
                setTimeout(() => {
                    btn.disabled = false;
                    btn.innerText = 'Ejecutar Prueba de Inundación';
                }, 2000);
            }
        }
    });
</script>

</body>
</html>