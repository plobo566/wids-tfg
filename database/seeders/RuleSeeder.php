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
            'name' => 'Fuerza Bruta login, tokens y otp',
            'class_name' => 'App\Services\Rules\BruteForceRule',
            'is_enabled' => true,
            'priority' => 10,
            'settings' => [
                'threshold' => 5,
                'window_minutes' => 2,
                'base_score' => 65,
                'reincidence' => 5,
                'critical_paths' => ['login', 'otp', 'password', 'token'],
            ],

        ]);


    }
}
