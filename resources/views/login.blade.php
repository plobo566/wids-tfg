<!DOCTYPE html>
<html>
<head>
    <title>Panel de Pruebas WIDS</title>
    

</head>

<body>

    <form action="{{route('login.post')}} " method="POST">
        @csrf
        <label>Prueba Login:</label>
        <br>
        <input type="text" name="inputprueba" placeholder="User">
        <br>
        <input type="text" name="inputprueba" placeholder="Password">
        <br><br>
        <button type="submit">Login</button>
    </form>


</body>
</html>