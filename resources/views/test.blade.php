<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Pruebas WIDS</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#1a1a1a] text-white font-sans antialiased min-h-screen flex items-center justify-center p-4">

    <div class="w-full max-w-md bg-[#1f1f1f] border border-gray-800 rounded-lg shadow-xl p-8">
        <div class="text-center mb-8">
            <h2 class="text-3xl font-extrabold tracking-tight text-amber-500">Endpoint Test</h2>
            <p class="font-light text-gray-400 mt-2 text-sm">Simulación de Inyección SQL (SQLi)</p>
        </div>

        <form action="{{ route('test.post') }}" method="POST" class="space-y-6">
            @csrf
            
            <div>
                <label for="inputprueba" class="block mb-2 text-sm font-medium text-gray-300">Payload de prueba:</label>
                <input type="text" name="inputprueba" id="inputprueba" placeholder="Ej: ' OR 1=1 --" required
                    class="bg-gray-800 border border-gray-700 text-white text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block w-full p-2.5 placeholder-gray-500 transition-colors">
            </div>

            <button type="submit"
                class="w-full text-white bg-amber-600 hover:bg-amber-500 focus:ring-4 focus:outline-none focus:ring-amber-800 font-medium rounded-lg text-sm px-5 py-2.5 text-center transition-colors">
                Lanzar Ataque
            </button>
        </form>
    </div>

</body>
</html>