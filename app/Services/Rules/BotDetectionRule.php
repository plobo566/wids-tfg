<?php

namespace App\Services\Rules;

use App\Models\Detection;

class BotDetectionRule implements DetectionRuleInterface{
    protected int $baseScore;
    protected int $reincidence;
    protected int $windowMinutes;


    public function setSettings(array $settings): void{
        $this->baseScore = $settings['base_score'] ?? 60;
        $this->reincidence = $settings['reincidence'] ?? 10;
        $this->windowMinutes = $settings['window_minutes'] ?? 60;
    }

    public function evaluate(array $data): ?array{

        $reasons = [];
        $method =strtoupper($data['method'] ?? 'GET');
        $suspiciousMethods = ['HEAD', 'OPTIONS', 'TRACE', 'PROPFIND', 'TRACK', 'PUT', 'DELETE'];


        if (in_array($method,$suspiciousMethods)){
            $reasons[]= "Uso de método HTTP sospechoso o inusual de sondeo: {$method}";
        }

        $userAgent = strtolower($data['user_agent'] ?? '');

        if (empty($userAgent) || $userAgent === 'null'){
            $reasons[] = 'User Agent vacío (Comportamiento habitual en scripts automatizados)';
        } else {
            $suspiciousSignatures = [
                'sqlmap', 'nikto', 'nessus', 'nmap', 'masscan', 
                'zmeu', 'w3af', 'acunetix', 'metasploit', 'dirbuster', 
                'wpscan', 'python-requests', 'python-urllib', 'scrapy', 
                'aiohttp', 'pycurl', 'curl/', 'wget/', 'httrack', 
                'libwww-perl', 'go-http-client'
            ];

            foreach ($suspiciousSignatures as $signature){
                if (str_contains($userAgent,$signature)){
                    $reasons[] = "Firma de herramienta sospechosa detectada en User Agent : {$signature}";
                    break;
                }
            }
        }

        if (empty($reasons)){
            return null;
        }


        $existing = Detection::where('rule_name', static::class)
            ->where('entity_value', $data['ip'])
            ->where('window_end', '>', now())
            ->first();

        $score = $this->baseScore;
        $finalReasons = $reasons;

        if ($existing) {
            $score = $existing->score + $this->reincidence;
            $oldReasons = $existing->details['reasons'] ?? [];
            $finalReasons = array_unique(array_merge($oldReasons, $reasons));
        }

        return [
            'type' => 'rule',
            'rule_name' => static::class,
            'entity_type' => 'ip',
            'entity_value' => $data['ip'],
            'score' => $score,
            'window_start' => now(),
            'window_end' => now()->addMinutes($this->windowMinutes),
            'details' => [
                'reasons' => array_values($finalReasons),
                'user_agent' => $data['user_agent'] ?? 'null',
                'method' => $method,
                'path' => $data['path'] ?? '/',
            ],
        ];


    }

}