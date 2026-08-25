<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LandingAuthController;

Route::get('/', function () {
    return view('landing');
});

Route::get('/test', function(){
    return view('test');
});


Route::post('/test',function(){
    return view('message',['title' => 'Post Realizado']);
})->name('test.post');

Route::get('/login', function () {
    return view('auth.login'); 
})->name('login');

Route::get('/register', function () {
    return view('auth.register'); 
})->name('register');

Route::post('/login', [LandingAuthController::class, 'login'])->name('login.post');

Route::post('/register', [LandingAuthController::class, 'register'])->name('register.post');

Route::post('/logout', [LandingAuthController::class, 'logout'])->name('logout');

Route::get('/xssattacker',function(){
    return view('xss');
});

Route::post('/xssattacker', function (\Illuminate\Http\Request $request) {
    return view('message', [
        'title' => 'Resultado del ataque XSS'
    ]);
})->name('xssattacker.post');


Route::get('/ratelimitattacker', function(){
    return view('ratelimit');
})->name('ratelimit.lab');

Route::get('/launch-csrf-trap', function () {
    $host = '127.0.0.1';
    $port = 8001;

  
    $conexion = @fsockopen($host, $port, $errno, $errstr, 1);

    if (is_resource($conexion)) {
        //abierto
        fclose($conexion);
    } else {
        //si cerrado encendemos
        $carpeta = base_path('CSRF');
        $comando = "nohup php -S {$host}:{$port} -t " . escapeshellarg($carpeta) . " > /dev/null 2>&1 &";
        exec($comando);
        
        sleep(1);
    }

    $dominioPublico = request()->getHost();

    return redirect()->away("https://{$dominioPublico}:{$port}");
});