<?php

namespace App\Services\Rules;

use App\Models\Detection;
use App\Models\SecurityEvent;

class BruteForceRule implements DetectionRuleInterface{

    protected int $threshold = 5;
    protected int $windowMinutes = 5;
    protected array $criticalPaths = ['login', 'otp', 'password', 'token']; //se cambiaran en la bbdd segun el endpoint que toque
    protected int $baseScore;
    protected int $reincidence;

    public function setSettings(array $settings): void{

        $this->threshold = $settings ['threshold'] ?? 5;
        $this->windowMinutes = $settings['window_minutes'] ?? 5;
        $this->criticalPaths = $settings['critical_paths'] ?? ['login', 'otp', 'password', 'token'];
        $this->baseScore = $settings['base_score'] ?? 60;
        $this->reincidence = $settings['reincidence']?? 5;
        
    }

    public function evaluate(array $data): ?array{

        
        $path = $data['path']?? '';
        $iscriticalPath = false;
        $url = $data['url'] ?? '';

        //comprobamos si path es critico
        foreach ($this->criticalPaths as $criticalPath){

            if (str_contains(strtolower($path), $criticalPath)){

                $iscriticalPath= true;
                break; //salimos del foreach

            }

        }


        if (!$iscriticalPath) return null;

        //intentos de la misma ip en el mismo endpoint

        $attempts = SecurityEvent::where('ip_address', $data['ip'])
            ->where('url', $url)
            ->where('created_at', '>=', now()->subMinutes($this->windowMinutes))
            ->count();



        if($attempts< $this->threshold) return null;

        //score

         $score = $this->baseScore;

        $existing = Detection::where('rule_name', static::class)
            ->where('entity_value', $data['ip'])
            ->where('window_end','>',now())
            ->first();


         if ($existing) {
          
            $score = $existing->score + $this->reincidence;
            
         }

         $score = min(100, $score);


         return [
            'type' => 'rule',
            'rule_name' => static::class,
            'entity_type' => 'ip',
            'entity_value' => $data['ip'],
            'score' => $score,
            'window_start' => now(),
            'window_end' => now()->addMinutes($this->windowMinutes),
            'details' => [
                'attempts' => $attempts,
                'path' => $path,
                'reincident' => (bool)$existing,
            ],
         ];
    }



}