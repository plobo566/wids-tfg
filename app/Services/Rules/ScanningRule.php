<?php

namespace App\Services\Rules;


use App\Models\SecurityEvent;
use App\Models\Detection;

class ScanningRule implements DetectionRuleInterface{

    protected int $threshold;
    protected int $windowMinutes;
    protected int $baseScore;
    protected int $reincidence;
    protected array $suspiciousPaths;
    protected array $errorCodes;


    public function setSettings(array $settings): void{

        $this->threshold = $settings['threshold'] ?? 3;
        $this->windowMinutes = $settings['window_minutes'] ?? 10;
        $this->baseScore = $settings['base_score'] ?? 50;
        $this->reincidence = $settings['reincidence'] ?? 5;
        $this->suspiciousPaths = $settings['suspicious_paths'] ?? [ '.env', '.git', 'phpmyadmin', 'wp-admin', 'config.php', 'database.sql', '.aws'];
        $this->errorCodes = $settings['error_codes'] ?? [401,403,404];

    }

    public function evaluate(array $data): ?array{

        $url = $data['url'] ?? '';
        $statusCode = $data['status_code'] ?? null;

        $isScanning = false;
        $isErrorScan = false;

        foreach ($this->suspiciousPaths as $suspiciousPath){
            if(str_contains(strtolower($url), $suspiciousPath)){
                $isScanning = true;
                break;
            }
        }

        if($statusCode && in_array((int)$statusCode, $this->errorCodes)){
            $isErrorScan = true;
        }


        if (!$isScanning && !$isErrorScan) return null;

        $attempts = SecurityEvent::where('ip_address', $data['ip'])
            ->where(function($query){ //parentesis en sql
                foreach ($this->suspiciousPaths as $suspiciousPath){
                    $query->orWhere('url', 'LIKE', "%{$suspiciousPath}%"); //OR ... LIKE %...% / los porcentajes son para caracteres antes o despues
                }
                $query->orWhereIn('status_code', $this->errorCodes);
            })
            ->where('created_at', '>=', now()->subMinutes($this->windowMinutes))
            ->count();


        if ($attempts < $this->threshold) return null;

        $existing = Detection::where('rule_name', static::class)
            ->where('entity_value', $data['ip'])
            ->where('window_end', '>', now())
            ->first();
        
        $score = $this->baseScore;

        if($existing){
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
                'url' => $url,
                'status_code' => $statusCode,
                'trigger' => $isScanning ? 'suspicious_path' : 'http_error',
            ],
        ];
    }

}