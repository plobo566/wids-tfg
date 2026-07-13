<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('landing');
});

Route::get('/test', function(){
    return view('test');
});


Route::post('/test',function(){
    return view('message',['title' => 'Post Realizado']);
})->name('test.post');

Route::get('/login',function(){
    return view('login');
});

Route::post('/login',function(){
    return view('message',['title' => 'Login realizado']);
})->name('login.post');

Route::get('/xssattacker',function(){
    return view('xss');
});


Route::get('/launch-csrf-trap', function () {
    $host = '127.0.0.1';
    $port = 8001;

    //servidor abierto?
    $conexion = @fsockopen($host, $port, $errno, $errstr, 1);

    if (is_resource($conexion)) {
        //si abierto cerramos comprobacion
        fclose($conexion);
    } else {
        //si cerrado abrimos
        $carpeta = base_path('CSRF');
        $comando = "nohup php -S {$host}:{$port} -t " . escapeshellarg($carpeta) . " > /dev/null 2>&1 &";
        exec($comando);
        

        sleep(1);
    }

    //web trampa
    return redirect()->away("http://{$host}:{$port}");
});
