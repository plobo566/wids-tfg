<?php

namespace App\Services\Rules;

use App\Models\Detection;
use App\Models\SecurityEvent; 
use Illuminate\Support\Facades\Cache;

class BruteForceRule implements DetectionRuleInterface{

    protected int $threshold;
    protected int $windowMinutes;
    protected int $baseScore;
    protected int $reincidence;

    public function setSettings(array $settings): void{
        $this->threshold = $settings['threshold'] ?? 5;
        $this->windowMinutes = $settings['window_minutes'] ?? 5;
        $this->baseScore = $settings['base_score'] ?? 60;
        $this->reincidence = $settings['reincidence'] ?? 15;
    }

    public function evaluate(array $data): ?array{

        if (($data['status_code'] ?? 200) !== 401 || !str_contains($data['path'] ?? '', 'login')) { 
            return null; 
        } 

        $failedAttempts = SecurityEvent::where('ip_address', $data['ip']) 
            ->where('url', 'LIKE', '%login%') 
            ->where('status_code', 401) 
            ->where('created_at', '>=', now()->subMinutes($this->windowMinutes)) 
            ->count(); 

        if ($failedAttempts < $this->threshold) return null;

        $score = $this->baseScore;
        $existing = Detection::where('rule_name', static::class)
            ->where('entity_value', $data['ip'])
            ->where('window_end', '>', now())
            ->first();

        if ($existing) {
            $score = $existing->score + $this->reincidence;
        }

        return [
            'type' => 'rule',
            'rule_name' => static::class,
            'entity_type' => 'ip',
            'entity_value' => $data['ip'],
            'score' => min(100, $score),
            'window_start' => now(),
            'window_end' => now()->addMinutes($this->windowMinutes),
            'details' => [
                'failed_attempts' => $failedAttempts,
                'path' => $data['path'] ?? '/login', 
                'reincident' => (bool)$existing,
            ],
        ];
    }
}