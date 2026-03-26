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
        Rule::create([
            'name' => 'Fuerza Bruta / Rate Limit',
            'class_name' => 'App\Services\Rules\RateLimitRule',
            'is_enabled' => true,
            'priority' => 100,
            'settings' => [
                'threshold' => 10,
                'windowSeconds' => 60,
            ],

        ]);
    }
}
