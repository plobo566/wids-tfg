<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Detection;
use Illuminate\Support\Str;

class RuleDistributionChart extends ChartWidget
{
    protected static ?string $heading = 'Distribución de Amenazas por Norma';
    protected static ?int $sort = 2; 
    protected int | string | array $columnSpan = 1; 
    protected static ?string $maxHeight = '230px';
    

    protected function getData(): array
    {
        $rawData = Detection::select('rule_name', \DB::raw('count(*) as count'))
            ->groupBy('rule_name')
            ->pluck('count', 'rule_name');

        $data = $rawData->mapWithKeys(function ($count, $ruleName) {
            $basename = class_basename($ruleName); // Quita "App\..."
            $readableName = Str::headline($basename); // Añade espacios "Sql Injection"
            
            return [$readableName => $count];
        });
        

        return [
            'datasets' => [
                [
                    'label' => 'Detecciones',
                    'data' => $data->values()->toArray(),
                    'backgroundColor' => [
                        '#ef4444', 
                        '#f59e0b', 
                        '#3b82f6', 
                        '#10b981', 
                        '#8b5cf6', 
                    ],
                ],
            ],
            'labels' => $data->keys()->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

   
    protected function getOptions(): array
    {
        return [
            'scales' => [
                'x' => ['display' => false],
                'y' => ['display' => false],
            ],
            'plugins' => [
                'legend' => [
                    'position' => 'bottom',
                ],
            ],
        ];
    }
}