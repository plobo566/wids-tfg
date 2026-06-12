<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\SecurityEvent;
use App\Models\Detection; 


class SecurityChart extends ChartWidget
{
    protected static ?string $heading = 'Evolución del Tráfico vs Ataques (Últimos 7 días)';
    protected static ?int $sort = 2; 
    protected int|string|array $columnSpan = 1;
    protected static ?string $maxHeight = '230px';

    protected function getData(): array
    {
        $days = [];
        $eventCounts = [];
        $detectionCounts = []; //nueva matriz para guardar las detecciones

        //ultimos 7 días
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $days[] = $date->format('d/m');

            //eventos en el dia
            $eventCounts[] = SecurityEvent::whereDate('created_at', $date->toDateString())->count();
            
            //detecciones
            $detectionCounts[] = Detection::whereDate('created_at', $date->toDateString())->count();
        }

        return [
            'datasets' => [
                [
                    'label' => 'Logs de Seguridad (Tráfico)',
                    'data' => $eventCounts,
                    'borderColor' => '#70d75eff', 
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'fill' => true,
                ],
                [
                    'label' => 'Detecciones (Ataques Reales)',
                    'data' => $detectionCounts,
                    'borderColor' => '#dc5540ff', 
                    'backgroundColor' => 'rgba(239, 68, 68, 0.1)',
                    'fill' => true,
                ],
            ],
            'labels' => $days,
        ];
    }

    protected function getType(): string
    {
        return 'line'; 
    }
}