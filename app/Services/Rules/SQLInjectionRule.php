<?php

namespace App\Services\Rules;
use App\Models\Detection;



class SqlInjectionRule implements DetectionRuleInterface{


    protected array $settings=[];
    protected int $lowWeight = 10;
    protected int $highWeight = 50;
    protected int $reincidence = 5;

    //PCRE

    protected array $patternslow = [
        '/(\'|--|#|\/\*|\*\/)/',  
    ];

    protected array $patternshigh = [
        '/\b(union|select|insert|update|delete|drop|alter)\b/i', // El /b es para que se permita 'selection' pero no 'select', i es para mayusculas
        '/\b(or|and)\b\s+[\d\w]+\s*=\s*[\d\w]+/i' // el /s+ busca 1 o mas espacios en blanco. el [\d\w]+ busca combinaciones de numeros o letras
    ];

    public function setSettings(array $settings): void{
        $this->lowWeight = $settings['low_weight'] ?? 10;
        $this->highWeight = $settings['high_weight'] ?? 50;
        $this->reincidence = $settings['reincidence'] ?? 10;
        
    }

    public function evaluate(array $data): ?array{



        if (!empty($data['is_internal_log'])) { 
            return null; 
        }

        $payload=json_encode($data['payload'] ?? []); //si no hay, ponemos array vacío
        $weight = 0;


        foreach ($this->patternslow as $pattern){
            if(preg_match($pattern,$payload)){
                $weight += $this->lowWeight;
            }
        }

        foreach ($this->patternshigh as $pattern){
            if(preg_match($pattern,$payload)){
                $weight += $this->highWeight;

            }
        }

        if ($weight===0){
            return null; //no se si dara problemas asi
        }


        $existing= Detection::where('rule_name', static::class)
            ->where('entity_value', $data['ip'])
            ->where('window_end','>', now())
            ->first();

        //$baseScore= $this->settings['base_score'] ?? 50;


        $score = min(100, $weight); //cada match 5 puntos. 1 match 55, 2 60 etc.

        if($existing){
            $score = min(100, $existing->score + $weight + $this->reincidence);
        }


        return [
            'type' => 'rule',
            'rule_name' => static::class,
            'entity_type' => 'ip',
            'entity_value' => $data['ip'],
            'score' => $score,
            'window_start' =>now(),
            'window_end' => now()->addminutes(30),
            'details' => [
                'weight' => $weight,
            ],
        ];
    }
}