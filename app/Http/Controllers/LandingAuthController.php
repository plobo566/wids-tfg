<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;
use App\Jobs\AnalyzeBruteForce;

class LandingAuthController extends Controller
{
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|email|unique:customers',
            'password' => 'required|min:6',
        ]);

        $customer = Customer::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        Auth::guard('landing')->login($customer);
        return redirect('/');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate(['email' => 'required|email', 'password' => 'required']);

        if (Auth::guard('landing')->attempt($credentials)) {
            $request->session()->regenerate(); //regenera id sesion
            $this->writeAuthLog($request, 'SUCCESS');  //llamo a generar logs login exito 
            return redirect('/');
        }
        $this->writeAuthLog($request, 'FAILED'); //llamo a generar logs login error 
        return back()->withErrors(['email' => 'Datos incorrectos.']);
    }


    public function logout(Request $request){
        Auth::guard('landing')->logout();

        $request->session()->invalidate(); //destruir sesion
        $request->session()->regenerateToken();

        return redirect('/');

    }



    private function writeAuthLog(Request $request, string $status): void
    {
        //ruta logs
        $logPath = storage_path('logs/wids_auth.log');

        $logData = json_encode([
            'timestamp' => now()->toDateTimeString(),
            'status'    => $status,
            'ip'        => $request->ip(),
            'email'     => $request->input('email'),
            'method'    => $request->method(),
        ]);

  
        File::append($logPath, $logData . PHP_EOL); //añadir datos y salto de linea (php_eol)

        // a partir de aqui seria trabajo  del agente 

      
    }

}