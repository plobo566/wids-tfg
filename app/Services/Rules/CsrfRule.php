<?php

namespace App\Services\Rules;

use App\Models\Detection;
use Illuminate\Support\Facades\URL;

class CsrfRule implements DetectionRuleInterface{

    protected int $baseScore;
    protected int $reincidence;
    protected int $windowMinutes;


    public function setSettings(array $settings): void{

        $this->baseScore = $settings['base_score'] ?? 75;
        $this->reincidence = $settings['reincidence'] ?? 10;
        $this->windowMinutes = $settings['window_minutes'] ?? 20;

    }

    public function evaluate(array $data): ?array {

        $method = strtoupper($data['method'] ?? 'GET');

        if ($method === 'GET'){
            return null;
        }


        $reasons = [];
        $origin = ($data['origin'] === 'direct') ? null : $data['origin'];
        $referer = ($data['referer'] === 'direct') ? null : $data['referer'];

        //obtener dominio con puerto
        
        $urlHost= parse_url($data['url'], PHP_URL_HOST);
        $urlPort= parse_url($data['url'], PHP_URL_PORT);
        $url= $urlPort? "$urlHost:$urlPort" : $urlHost;


        if(empty($origin) && empty($referer)){
            $reasons[] = 'Ausencia de cabeceras en petición de cambio de estado';
        } else {

            $source = $origin ? $origin : $referer;
            $sourceHost = parse_url($source,PHP_URL_HOST) ?? 'desconocido';
            $sourcePort = parse_url($source,PHP_URL_PORT);
            $sourceUrl = $sourcePort? "$sourceHost:$sourcePort" : $sourceHost;

            if ($sourceUrl !== $url){

               
                $reasons[] = "Petición cruzada de dominio externo: $sourceUrl";

            }

        }

        if (empty($reasons)) return null;

        $existing= Detection::where('rule_name', static::class)
            ->where('entity_value', $data['ip'])
            ->where('window_end','>', now())
            ->first();

        $score = $this->baseScore;
        $finalReasons = $reasons;


        if($existing){
            $score = min (100, $existing->score + $this->reincidence);
            $oldReasons = $existing->details['reasons'] ?? [];
            $finalReasons = array_unique(array_merge($oldReasons,$reasons));
        }



        return [
            'type' => 'rule',
            'rule_name' => static::class,
            'entity_type' => 'ip',
            'entity_value' => $data['ip'],
            'score' => $score,
            'window_start' =>now(),
            'window_end' => now()->addminutes($this->windowMinutes),
            'details' => [
                'reasons' => array_values($finalReasons),
                'method' => $method,
                'target_url' => $data['url'],
                'source' => $origin ? $origin: ($referer? $referer: 'direct/none')
            ],
        ];
    }


}