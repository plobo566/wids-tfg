<!DOCTYPE html>
<html lang="es" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | WIDS Lab</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#0f0f0f] text-gray-200 min-h-screen flex items-center justify-center p-6">

    <div class="w-full max-w-md bg-[#1f1f1f] p-8 rounded-xl shadow-2xl border border-gray-800">
        
        <h2 class="text-2xl font-bold text-white text-center mb-6">Autenticación</h2>
        <p class="text-gray-400 text-sm text-center mb-8">Acceso al entorno de pruebas del sistema.</p>

        <form action="{{ route('login.post') }}" method="POST" class="space-y-6">
            @csrf
            
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">Usuario</label>
                <input type="text" name="username" placeholder="admin" 
                       class="w-full px-4 py-3 bg-[#121212] border border-gray-700 rounded-lg text-white focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none transition-all">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">Contraseña</label>
                <input type="password" name="password" placeholder="••••••••" 
                       class="w-full px-4 py-3 bg-[#121212] border border-gray-700 rounded-lg text-white focus:ring-2 focus:ring-amber-500 focus:border-amber-500 outline-none transition-all">
            </div>

            <button type="submit" 
                    class="w-full bg-amber-600 hover:bg-amber-500 text-white font-bold py-3 rounded-lg transition-all transform hover:scale-[1.02] active:scale-95 mt-4">
                Iniciar Sesión
            </button>
        </form>

        <div class="mt-6 text-center">
            <a href="/" class="text-xs text-gray-500 hover:text-amber-500 transition-colors">Volver al Dashboard</a>
        </div>
    </div>

</body>
</html>