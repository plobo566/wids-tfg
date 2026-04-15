<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/test', function(){
    return view('test');
});


Route::post('/test',function(){
    return "Post realizado";
})->name('test.post');

Route::get('/login',function(){
    return view('login');
});

Route::post('/login',function(){
    return "Login realizado";
})->name('login.post');