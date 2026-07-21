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
        <h2 class="text-2xl font-bold text-white text-center mb-6">Crear Cuenta</h2>

        <form action="{{ route('register.post') }}" method="POST" class="space-y-5">
            @csrf
            <div>
                <label class="block text-sm text-gray-300 mb-1">Nombre</label>
                <input type="text" name="name" required class="w-full px-4 py-2 bg-[#121212] border border-gray-700 rounded-lg text-white">
            </div>
            <div>
                <label class="block text-sm text-gray-300 mb-1">Email</label>
                <input type="email" name="email" required class="w-full px-4 py-2 bg-[#121212] border border-gray-700 rounded-lg text-white">
            </div>
            <div>
                <label class="block text-sm text-gray-300 mb-1">Contraseña</label>
                <input type="password" name="password" required class="w-full px-4 py-2 bg-[#121212] border border-gray-700 rounded-lg text-white">
            </div>
            <button type="submit" class="w-full bg-amber-600 hover:bg-amber-500 text-white font-bold py-3 rounded-lg mt-2">Registrarse</button>
        </form>
    </div>
</body>
</html>