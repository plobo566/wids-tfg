<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SecurityEvent;
use App\Models\Detection;
use App\Models\Incident;

class DetectBehaviorAnomalies extends Command
{
    protected $signature = 'wids:detect-anomalies';
    protected $description = 'Analiza desviaciones en el comportamiento y volumen de peticiones por IP';

    public function handle()
    {
        //Calcular volumen total de peticiones en las ultimas 24 horas para conseguir la media
        $dayAgo = now()->subDay();
        
        $totalEvents = SecurityEvent::where('created_at', '>=', $dayAgo)->count();
        $totalUniqueIps = SecurityEvent::where('created_at', '>=', $dayAgo)->distinct('ip_address')->count('ip_address');

        if ($totalUniqueIps === 0 || $totalEvents === 0) {
            $this->info('No hay suficientes datos históricos para calcular anomalías.');
            return;
        }

        // media de peticiones por IP en 1 dia
        $averageRequestsPerIp = $totalEvents / $totalUniqueIps;
        
        // por hora
        $hourlyAverage = $averageRequestsPerIp / 24;

        //4 veces más de la media es una anomalia
        $thresholdFactor = 4;
        $maxAllowedHourlyRequests = max(50, $hourlyAverage * $thresholdFactor); // minimo 50 peticiones

        //Analizamos el tráfico real de la ultima hora por cada IP activa
        $oneHourAgo = now()->subHour();
        
        $recentTraffic = SecurityEvent::where('created_at', '>=', $oneHourAgo)
            ->select('ip_address', \DB::raw('count(*) as total_requests'))
            ->groupBy('ip_address')
            ->get();

        foreach ($recentTraffic as $traffic) {
            if ($traffic->total_requests > $maxAllowedHourlyRequests) {
                
                //si hay anomalia la registramos
                $existing = Detection::where('rule_name', 'AnomalyVolumeDetection')
                    ->where('entity_value', $traffic->ip_address)
                    ->where('window_end', '>=', now())
                    ->first();

                $details = [
                    'hourly_average_baseline' => round($hourlyAverage, 2),
                    'threshold_limit' => round($maxAllowedHourlyRequests, 2),
                    'actual_requests' => $traffic->total_requests,
                    'deviation_percentage' => round(($traffic->total_requests / $hourlyAverage) * 100, 2) . '%'
                ];


                $deviationValue = ($traffic->total_requests / $hourlyAverage) * 100;
                $baseScore = 60;   
                $extraScore = (($deviationValue - 400) / 100) * 10;
                $dynamicScore = min(100, $baseScore + $extraScore);

                if ($existing) {
                    $existing->update([
                        'score' => $dynamicScore,
                        'details' => $details,
                        'window_end' => now()->addMinutes(30)
                    ]);
                    $targetDetection = $existing;
                } else {
                    $targetDetection = Detection::create([
                        'type' => 'anomaly', //tipo anomaly
                        'rule_name' => 'AnomalyVolumeDetection',
                        'entity_type' => 'ip',
                        'entity_value' => $traffic->ip_address,
                        'score' => $dynamicScore,
                        'window_start' => now(),
                        'window_end' => now()->addMinutes(30),
                        'details' => $details
                    ]);
                }

                //Lo ponemos tambien con el sistema de incidentes 
                $incident = Incident::where('entity_value', $targetDetection->entity_value)
                    ->where('status', 'open')
                    ->first();

                if ($incident) {
                    $incident->update(['last_seen' => now()]);
                } else {
                    $incident = Incident::create([
                        'title' => 'Comportamiento anómalo: Volumen de tráfico inusual',
                        'entity_value' => $targetDetection->entity_value,
                        'status' => 'open',
                        'severity' => 'medium',
                        'first_seen' => now(),
                        'last_seen' => now()
                    ]);
                }

                $targetDetection->update(['incident_id' => $incident->id]);
                
                $this->warn("Anomalía detectada para la IP: {$traffic->ip_address} ({$traffic->total_requests} peticiones)");
            }
        }

        $this->info('Análisis de comportamiento finalizado con éxito.');
    }
}