<?php

namespace App\Services\Rules;

use App\Models\SecurityEvent;
use Illuminate\Support\Facades\Cache;

class RateLimitRule implements DetectionRuleInterface{
    protected int $threshold; //umbral: numero peticiones en menos de $windowSeconds segundos
    protected int $windowSeconds; //tiempo de comparación

    public function __construct(int $threshold=10, int $windowSeconds=60){
        $this->threshold=$threshold;
        $this->windowSeconds = $windowSeconds;

        if ($threshold <=0) {
            throw new \InvalidArgumentException('Threshold must be >0');
        }
    }

    public function setSettings(array $settings): void{
        $this->threshold = $settings['threshold'] ?? $this->threshold; //los ?? son por si no hubiera entrada en la bbdd
        $this->windowSeconds = $settings['windowSeconds'] ?? $this->windowSeconds;
    }

    public function evaluate(array $data): ?array{


        if (!empty($data['is_internal_log'])) { 
            return null; 
        }

        $ip=$data['ip'];

        $windowStart= now();
        $windowEnd = now()->addSeconds($this->windowSeconds);

        $count= SecurityEvent::where('ip_address', $ip) 
            ->where('created_at','>=', now()->subSeconds($this->windowSeconds)) 
            ->count();
        

        
        // es un sigmoide
        //el score como máximo tendra valor 100
        //cuando pasa el tiempo windowseconds se reinicia la cuenta de score

        $T = $this->threshold;

        if ($count <= $T) {
            $score = 0;
            return null;
        
        }

        $cacheKey= 'cooldown_ratelimit_'.$ip;

        if(!Cache::add($cacheKey,true,1)){
            return null;
        }

        $excess = $count - $T;
        $k= 3; //pendiente curva
        $x=$excess/(9*$T);
        $x =min($x,1); 
        
        $score = 100 * (1 / (1+exp(-$k * ($x - 0.5))));
        
   

        return [
            'type' => 'rule',
            'rule_name' => static::class,
            'entity_type' => 'ip',
            'entity_value' => $ip,
            'score' => $score,
            'window_start' => $windowStart,
            'window_end' => $windowEnd,
            'details' => [
                'count' => $count,
                'threshold' => $this->threshold,
            ],
        ];

    }
}