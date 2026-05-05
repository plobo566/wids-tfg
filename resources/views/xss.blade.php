<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Pruebas Exclusivo XSS</title>
    <style>
        body { font-family: sans-serif; background-color: #f4f4f4; padding: 20px; }
        .container { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); max-width: 600px; margin: auto; }
        h1 { color: #333; text-align: center; }
        h3 { color: #666; margin-top: 25px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input[type="text"], textarea { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
        textarea { height: 100px; resize: vertical; }
        button { background-color: #d9534f; color: white; padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer; width: 100%; font-size: 16px; }
        button:hover { background-color: #c9302c; }
        .payload-examples { background-color: #e9e9e9; padding: 10px; border-radius: 4px; font-family: monospace; font-size: 0.9em; margin-bottom: 20px; border-left: 5px solid #d9534f;}
        .payload-examples strong { color: #a94442; }
    </style>
</head>
<body>

<div class="container">
    <h1>Laboratorio de Inyección XSS</h1>
    <p>Esta página simula un formulario legítimo (ej: comentarios, perfil) que es vulnerable a XSS porque no limpia los datos de entrada. Tu WIDS debe detectar el ataque en el servidor.</p>

    <div class="payload-examples">
        <strong>Payloads de prueba (copia y pega en los campos):</strong><br><br>
        1. <code>&lt;script&gt;alert('Hacked')&lt;/script&gt;</code> (Categoría: Etiquetas)<br>
        2. <code>&lt;img src=x onerror=alert(1)&gt;</code> (Categoría: Event Handlers)<br>
        3. <code>&lt;svg onload=alert(1)&gt;</code> (Categoría: Event Handlers/Etiquetas)<br>
        4. <code>javascript:alert('XSS')</code> (Categoría: Protocolos - úsalo en el campo 'Web')
    </div>

    <form action="/login" method="POST">
        
        {{ csrf_field() }}

        <div class="form-group">
            <label for="nombre">Nombre de usuario (Prueba de Etiquetas):</label>
            <input type="text" id="nombre" name="username" placeholder="Ej: <script>...</script>">
        </div>

        <div class="form-group">
            <label for="bio">Biografía (Prueba de Event Handlers):</label>
            <textarea id="bio" name="bio" placeholder="Ej: <img src=x onerror=...>"></textarea>
        </div>

        <div class="form-group">
            <label for="web">Página Web (Prueba de Protocolos):</label>
            <input type="text" id="web" name="website" placeholder="Ej: javascript:...">
        </div>

        <button type="submit">Enviar Ataque XSS</button>
    </form>
</div>

</body>
</html>