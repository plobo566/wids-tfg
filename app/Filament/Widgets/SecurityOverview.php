<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Incident;
use App\Models\SecurityEvent;
use App\Models\Detection;

class SecurityOverview extends BaseWidget
{

    protected static ?int $sort = 1;

    protected function getStats(): array
    {

        return [
            Stat::make('Incidentes Activos', Incident::where('status', 'open')->count())
                ->description('Expedientes abiertos requiriendo atención')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color('danger'),

            Stat::make('Eventos Registrados', SecurityEvent::count())
                ->description('Total de logs analizados por el WIDS')
                ->descriptionIcon('heroicon-m-shield-check')
                ->color('success'),

            Stat::make('Anomalías de Volumen', Detection::where('type', 'anomaly')->count())
                ->description('Alertas por desviaciones de tráfico')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color('warning'),
        ];
    }
}