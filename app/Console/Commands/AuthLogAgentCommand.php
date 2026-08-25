<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Cache;
use App\Models\SecurityEvent;
use App\Jobs\AnalyzeSecurityEvent;
use App\Services\DataNormalizer;

class AuthLogAgentCommand extends Command {


    protected $signature = 'wids:auth-agent';
    protected $description = 'Demonio que vigila y procesa logs externos de autenticación';
 
    public function handle(){

        $logPath = storage_path('logs/wids_auth.log');
        $normalizer = app(DataNormalizer::class);

        while(true){
            clearstatcache(false, $logPath); //limpiamos cache para que el tamaño se actualice

            if (!File::exists($logPath)){
                sleep(1); //no hay logins asi que esperamos
                continue;
            }


            $lastPosition = Cache::get('wids_auth_log_position',0); //primera vez 0 luego logposition
            $currentSize = filesize($logPath);

            if ($currentSize < $lastPosition) { //vaciado log
                $lastPosition = 0;

            }


            if ($currentSize > $lastPosition){
                $handle = fopen($logPath,'r'); //abrir modo lectura puntero 0
                fseek($handle, $lastPosition); //puntero ultima pos
                

                while (($line = fgets($handle)) !== false){ //leer linea a linea con fgets

                    $line = trim($line);
                    if(empty($line)) continue;


                    $log = json_decode($line, true);
                    if (!$log) continue;


                    $isFailed = ($log['status']?? '') === 'FAILED';

                    $rawData = [
                        'ip_address'  => $log['ip'] ?? '0.0.0.0', 
                        'method'      => $log['method'] ?? 'POST', 
                        'url'         => '/login', 
                        'user_agent'  => 'WIDS-Auth-Agent', 
                        'status_code' => $isFailed ? 401 : 200, 
                        'payload'     => [ 
                            'email'  => $log['email'] ?? '', 
                            'status' => $log['status'] ?? '', 
                            'source' => 'wids_auth.log' 
                        ], 
                    ]; 
                    


                    $event = new SecurityEvent([
                    'ip_address'  => $rawData['ip_address'],
                    'method'      => $rawData['method'],
                    'url'         => $rawData['url'],
                    'user_agent'  => $rawData['user_agent'],
                    'status_code' => $rawData['status_code'],
                    'payload'     => $rawData['payload'],
                    ]);
                    $timestamp = $log['timestamp'] ?? now();
                    $event->created_at = $timestamp;
                    $event->updated_at = $timestamp;
                    $event->timestamps = false;
                    $event->save();


                    $normalizedData = $normalizer->normalize($rawData);



                    if ($isFailed) { 
                        AnalyzeSecurityEvent::dispatch([ 
                            'ip'          => $normalizedData['ip_address'], 
                            'user_agent'  => $normalizedData['user_agent'], 
                            'payload'     => $normalizedData['payload'], 
                            'path'        => 'login', 
                            'method'      => $normalizedData['method'], 
                            'url'         => $normalizedData['url'], 
                            'referer'     => 'direct', 
                            'origin'      => 'direct', 
                            'status_code' => $normalizedData['status_code'], 
                            'is_internal_log' => true,
                        ], $event->id); 
                    }

                }

                Cache::put('wids_auth_log_position', ftell($handle)); //ftell dice posicion puntero y guardamos en widsposition
                fclose($handle);//cerrar lectura

            }

            sleep(1); //cada segundo comprobamos 
        }
    }

}