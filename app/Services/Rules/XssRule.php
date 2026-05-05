<?php

namespace App\Services\Rules;

use App\Models\Detection;

class XssRule implements DetectionRuleInterface{

    protected int $baseScore;
    protected int $reincidence;
    protected int $windowMinutes;

    public function setSettings(array $settings): void{

        $this->baseScore = $settings['base_score'] ?? 70;
        $this->reincidence = $settings['reincidence'] ?? 5;
        $this->windowMinutes = $settings['window_minutes'] ?? 15;
    }


    public function evaluate(array $data): ?array{

        $payload = json_encode($data['payload'] ?? []);
        $reasons = [];

        if(preg_match('/<(script|iframe|object|embed|svg|details|style)/i', $payload)){
            $reasons[]= 'Inyeccion de etiquetas ejecutables'; //han escrito script. Eso implica formulario con código.
        }

        if(preg_match('/on[a-z]+\s*=/i', $payload)){ //on...= para detectar onclick= , onbegin= etc
            $reasons[]= 'Uso de Event Handlers'; //en caso de evento los event handlers saltan. si alguien pone un eventhandler en el payload es sospechoso.
        }

        if(preg_match('/(javascript|data|vbscript):/i', $payload)){ //javascript: ejecuta codigo
            $reasons[]= 'Uso de protocolos de scripting en atributos'; //donde deberia ir una direccion web han puesto codigo. href= "javascript:..."
        }


        if(empty($reasons)) return null;



        $existing = Detection::where('rule_name',static::class)
            ->where('entity_value', $data['ip'])
            ->where('window_end', '>', now())
            ->first();


        

        
        $score = $this->baseScore;
        $finalReasons=$reasons;
        if($existing){

            $score=$existing->score +$this->reincidence;

            $oldReasons = $existing->details['reasons'] ?? [];

            $finalReasons = array_unique(array_merge($oldReasons,$reasons)); //quitamos duplicados y juntamos arrays

   

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
                'reasons' => $finalReasons,
                'payload_preview' => substr($payload, 0, 150), //saturamos en 150 caracteres de payload
            ],
         ];
    }
}