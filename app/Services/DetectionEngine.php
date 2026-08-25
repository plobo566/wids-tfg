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
use App\Models\AlertSetting;

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

            //incidents

            $incident = \App\Models\Incident::where('entity_value', $targetDetection->entity_value)
                ->where('status', 'open')
                ->first();

            $severity = match(true) {
                $targetDetection->score >=   100 => 'critical',
                $targetDetection->score >= 80 => 'high',
                $targetDetection->score >= 50 => 'medium',
                default => 'low',
            };

            if ($incident) {
                $incident->update([
                    'last_seen' => now(),
                    'severity' => match(true) {
                        $incident->severity === 'critical' || $severity === 'critical' => 'critical',
                        $incident->severity === 'high' || $severity === 'high' => 'high',
                        $incident->severity === 'medium' || $severity === 'medium' => 'medium',
                        default => 'low',
                    }
                ]);
            } else {
                $incident = \App\Models\Incident::create([
                    'title' => 'Actividad sospechosa detectada: ' . class_basename($targetDetection->rule_name),
                    'entity_value' => $targetDetection->entity_value,
                    'status' => 'open',
                    'severity' => $severity,
                    'first_seen' => now(),
                    'last_seen' => now(),
                ]);
            }

            $targetDetection->update(['incident_id' => $incident->id]);



            //webhook

            $config = AlertSetting::first();
            \Log::info('--- DEBÚGUEANDO WEBHOOK ---');
            \Log::info('¿Existe configuración en la DB?: ' . ($config ? 'SÍ' : 'NO'));
            \Log::info('Score obtenido: ' . $targetDetection->score . ' | Threshold requerido: ' . ($config ? $config->threshold : 'N/A'));
            //comprobamos si existe la configuracion y si la puntuación supera el baremo
            if ($config && $targetDetection->score >= $config->threshold) {
                \Log::info('¡Condiciones superadas! Intentando encolar correo...');

                $emailCooldownKey = 'email_alert_sent_' . $targetDetection->id;
                
                // cooldown de 1 hora para no spam
                if (Cache::add($emailCooldownKey, true, 3600)) {
                    // enviar al correo del panel
                    Mail::to($config->email_destination)->send(new CriticalAlertMail($targetDetection));
                }
            }
                
           }
        }

        return $detections;
    }
}