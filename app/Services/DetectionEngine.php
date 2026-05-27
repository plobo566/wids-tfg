<?php

namespace App\Services;

use Illuminate\Support\Facades\File;
use App\Services\Rules\DetectionRuleInterface;
use App\Models\Detection;
use App\Models\Rule;
use Illuminate\Support\Facades\Log;
use App\Mail\CriticalAlertMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;

class DetectionEngine{

    protected array $rules = [];

  

    //public function addRule(DetectionRuleInterface $rule){
    //    $this->rules[] = $rule;
    //}


    public function __construct(){
        //Log::info('constructor ejecutado');
    }

    protected function loadRules(): void{


        $activeRules = Rule::where('is_enabled', true)->get();

        
        //$path = app_path('Services/Rules');
        //$files = File::files($path); //devuelve objeto tipo 'Symfony\Component\Finder\SplFileInfo' mirando con el dd

        foreach ($activeRules as $activeRule){
            //$name=$file->getFilenameWithoutExtension();

            //$class = 
            // 'App\\Services\\Rules\\' .
            //$name;
            
            $class= $activeRule->class_name;

            

            
             
            if (!class_exists($class) || interface_exists($class)){
                continue; //terminamos el foreach si no existe la clase o es una interfaz
            }

            $rule = app($class); //manera optima de instanciar objeto de las reglas 

            if (method_exists($rule, 'setSettings')){
                $rule->setSettings($activeRule->settings); //si tiene el metodo setSettings lo usamos
            }
            
            if ($rule instanceof DetectionRuleInterface){
                $this->rules[] = $rule; //solo si cumple la interfaz la añadimos 
            }

        }

    }



    public function evaluate(array $data, int $eventId): array{


        $this->rules = [];
        $this->loadRules();

        $detections=[];//reset detecciones

        foreach ($this->rules as $rule){
           $result = $rule->evaluate($data);

           if ($result !== null && $result['score'] !== 0){
            
            $existing= Detection::where('rule_name', $result['rule_name'])
                ->where('entity_type', $result['entity_type'])
                ->where('entity_value',$result['entity_value'])
                ->where('window_end', '>=',now())
                ->first();

            if ($existing){
                $existing->update([
                    'security_event_id' => $eventId,
                    'score' => max($existing->score, $result['score']),
                    'details' => $result['details'],
                    'window_end' => $result['window_end'],
                ]);
            
                $detections[] = $existing;
                $targetDetection = $existing;

            }else{
                $result['security_event_id'] = $eventId;
                $detection = Detection::create($result);
                $detections[] = $detection;
                $targetDetection = $detection;

            }

            if ($targetDetection->score >= 100) {
                $emailCooldownKey = 'email_alert_sent_' . $targetDetection->id;
                
                if (Cache::add($emailCooldownKey, true, 3600)) {
                    Mail::to('pablete566@gmail.com')->send(new CriticalAlertMail($targetDetection));
                }
            }
                
           }
        }

        return $detections;
    }
}