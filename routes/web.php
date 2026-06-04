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