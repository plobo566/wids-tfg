<!DOCTYPE html>
<html>
<head>
    <title>Panel de Pruebas WIDS</title>
    

</head>

<body>

    <form action="{{route('test.post')}} " method="POST">
        @csrf
        <label>Prueba SQLi:</label>
        <br>
        <input type="text" name="inputprueba" placeholder="1=1">
        <br><br>
        <button type="submit">Enviar</button>
    </form>


</body>
</html>