<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Rule;
use App\Services\Rules\RateLimitRule;

class RuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        Rule::truncate(); //las elimino porque no las actualiza laravel por algun motivo

        Rule::updateOrCreate([ //no se porque no actualiza las columnas y crea nuevas
            'name' => 'Fuerza Bruta / Rate Limit',
            'class_name' => 'App\Services\Rules\RateLimitRule',
            'is_enabled' => true,
            'priority' => 10,
            'settings' => [
                'threshold' => 10,
                'windowSeconds' => 60,
            ],

        ]);


        Rule::updateOrCreate([
            'name' => 'Inyeccion SQL / SQLi',
            'class_name' => 'App\Services\Rules\SQLInjectionRule',
            'is_enabled' => true,
            'priority' => 10,
            'settings' => [
                'low_weight' => 15,
                'high_weight' => 60,
                'reincidence_bonus' => 5,
            ],

        ]);




        Rule::updateOrCreate([
            'name' => 'Fuerza Bruta login',
            'class_name' => 'App\Services\Rules\BruteForceRule',
            'is_enabled' => true,
            'priority' => 10,
            'settings' => [
                'threshold' => 5,
                'window_minutes' => 2,
                'base_score' => 5,
                'reincidence' => 5,
            ],

        ]);


        Rule::updateOrCreate([
            'name' => 'Escaneo de Endpoints',
            'class_name' => 'App\Services\Rules\ScanningRule',
            'is_enabled' => true,
            'priority' => 10,
            'settings' => [
                'threshold' => 2,
                'window_minutes' => 5,
                'base_score' => 50,
                'reincidence' => 5,
                'suspicious_paths' => ['.env', '.git', 'phpmyadmin', 'wp-admin', 'config.php', 'database.sql', '.aws'],
                'error_codes' => ['401,403,404'],
            ],

        ]);



        Rule::updateOrCreate([
            'name' => 'XSS | Cross-Site Scritpting ',
            'class_name' => 'App\Services\Rules\XssRule',
            'is_enabled' => true,
            'priority' => 10,
            'settings' => [
                'base_score' => 70,
                'reincidence' => 10,
                'window_minutes' => 10,
            ],

        ]);


        Rule::updateOrCreate([
            'name' => 'CRSF | Cross-Site Request Forgery ',
            'class_name' => 'App\Services\Rules\CsrfRule',
            'is_enabled' => true,
            'priority' => 10,
            'settings' => [
                'base_score' => 70,
                'reincidence' => 10,
                'window_minutes' => 20,
            ],

        ]);


        Rule::updateOrCreate([
            'name' => 'Detector de Enumeración de usuarios | User Enumeration ',
            'class_name' => 'App\Services\Rules\UserEnumerationRule',
            'is_enabled' => true,
            'priority' => 10,
            'settings' => [
                'threshold' => 3,
                'base_score' => 20,
                'reincidence' => 10,
                'window_minutes' => 15,
                'user_fields' => ['username', 'email', 'user', 'usuario', 'login']
            ],

        ]);



        Rule::updateOrCreate([
            'name' => 'Detector de Bots | Bot Detection ',
            'class_name' => 'App\Services\Rules\BotDetectionRule',
            'is_enabled' => true,
            'priority' => 10,
            'settings' => [
                'base_score' => 20,
                'reincidence' => 10,
                'window_minutes' => 15,
            ],

        ]);

    }
}
