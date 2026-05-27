<?php

namespace App\Filament\Widgets;

use App\Models\Detection;
use App\Models\Rule;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DashboardStats extends BaseWidget
{
  
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        //detecciones de hoy
        $deteccionesHoy = Detection::whereDate('created_at', today())->count();
        
        //reglas activas
        $reglasActivas = Rule::where('is_enabled', true)->count();
        
        //total
        $deteccionesTotales = Detection::count();

        return [
            Stat::make('Detecciones Hoy', $deteccionesHoy)
                ->description($deteccionesHoy > 0 ? 'Ataques registrados hoy' : 'Sistema limpio')
                ->descriptionIcon($deteccionesHoy > 0 ? 'heroicon-m-shield-exclamation' : 'heroicon-m-shield-check')
                ->color($deteccionesHoy > 0 ? 'danger' : 'success')
                ->chart([7, 2, 10, 3, 15, 4, $deteccionesHoy]), //grafica visual (decorativo)

            Stat::make('Reglas Activas', $reglasActivas)
                ->description('Motor de detección operativo')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('success'),

            Stat::make('Histórico Total', $deteccionesTotales)
                ->description('Detecciones desde el inicio')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color('primary'),
        ];
    }
}