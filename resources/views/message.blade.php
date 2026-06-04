<!DOCTYPE html>
<html lang="es" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#0f0f0f] text-gray-200 min-h-screen flex items-center justify-center p-6">

    <div class="w-full max-w-md bg-[#1f1f1f] p-8 rounded-xl shadow-2xl border border-gray-800 text-center">
        <div class="text-amber-500 text-5xl mb-4">✓</div>
        <h2 class="text-2xl font-bold text-white mb-4">{{ $title }}</h2>
        <p class="text-gray-400 mb-8">El proceso se ha completado correctamente en el sistema.</p>
        <a href="/" class="block w-full bg-amber-600 hover:bg-amber-500 text-white font-bold py-3 rounded-lg transition-all">
            Volver al Inicio
        </a>
    </div>

</body>
</html>