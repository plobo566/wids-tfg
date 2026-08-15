<?php

namespace App\Services\Rules;

use App\Models\Detection;
use App\Models\SecurityEvent;


class UserEnumerationRule implements DetectionRuleInterface{

    protected int $threshold;
    protected int $windowMinutes;
    protected int $baseScore;
    protected int $reincidence;
    protected array $userFields;

    public function setSettings(array $settings): void{

        $this->threshold = $settings['threshold'] ?? 5;
        $this->windowMinutes = $settings['window_minutes'] ?? 10;
        $this->baseScore = $settings['base_score'] ?? 20;
        $this->reincidence = $settings['reincidence'] ?? 10;
        $this->userFields = $settings['user_fields'] ?? ['username', 'user', 'email', 'login', 'usuario'];
        
    }


    public function evaluate(array $data): ?array{


        if (!empty($data['is_internal_log'])) { 
            return null; 
        }

        if (!str_contains(strtolower($data['url'] ?? ''), 'login')){
            //dump("DEBUG: no es ruta login" . $data['url']);

            return null;
        }


        $ip = $data['ip'];
        $payload = $data['payload'];

        if (is_string($payload)){
            $payload = json_decode($payload, true) ?? [];
        }


        $currentUser = null;

        foreach($this->userFields as $field){
            if(!empty($payload[$field])){
                $currentUser = $payload[$field];
                break;
            }
        }

        if (!$currentUser){
            
            //dump("DEBUG: no se encontro usuario en el payload" , $this->userFields);
            //dump ("payload" , $payload);

            return null;
        }


        $events = SecurityEvent::where('ip_address', $ip) //cada login de esa ip en ese rango de tiempo
            ->where('url', 'LIKE', '%login%')
            ->where('created_at','>=', now()->subMinutes($this->windowMinutes))
            ->get();

        //dump("DEBUG: Eventos encontrados en DB para la IP $ip: " . $events->count());


        $arrayUsers = [];

        foreach ($events as $event){ 
            $payloadevent = $event->payload; //payload de cada request

            if(is_string($payloadevent)){
                $payloadevent = json_decode($payloadevent, true) ?? [];
            }

            foreach($this->userFields as $userField){
                if(!empty($payloadevent[$userField]) && isset($payloadevent[$userField])){  //compruebo cada posible nombre de campo. p ej. 'username' o 'email'. (solo 1 deberia dar coincidenciaa)
                    $arrayUsers[$payloadevent[$userField]] = true; //lookup table. las llaves del array son el nombre que se ha introducido en el campo de 'user' . si hay algo lo ponemos en true.
                    break;
                }
            }
        }
        $distinctUsers = count($arrayUsers);

        //dump("DEBUG: Usuarios unicos contados" . $distinctUsers);
        //dump("lista de users detectados: ", array_keys($arrayUsers));
        



        if ($distinctUsers < $this->threshold){
            return null;
        }

        //dump("umbral superado");

        
        $existing = Detection::where('rule_name', static::class)
            ->where('entity_value', $ip)
            ->where('window_end', '>', now())
            ->first();


        $score = $this->baseScore;
        $reasons = ["Intento de enumeración: $distinctUsers usuarios diferentes probados"];
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
                'reasons' => $reasons,
                'distinct_users_count' => $distinctUsers,
                'last_user_tried' => $currentUser,
                ],
        ];

    }


}