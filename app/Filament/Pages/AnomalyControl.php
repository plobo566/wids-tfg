<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Forms\Form;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Filament\Notifications\Notification;
use App\Console\Commands\DetectBehaviorAnomalies;

class AnomalyControl extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-trending-up';
    protected static ?string $navigationLabel = 'Escaneo de Anomalias';
    protected static ?string $title = 'Panel de Control de Anomalias';
    protected static ?string $navigationGroup = 'Sistema';
    
    protected static string $view = 'filament.pages.anomaly-control';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'auto_run' => Cache::get('wids_auto_run', false),
            'interval' => Cache::get('wids_interval', 'daily'),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Ejecución Automática')
                    ->description('Configura el motor para que analice el tráfico en segundo plano de forma autónoma sin intervención manual.')
                    ->icon('heroicon-o-clock')
                    ->schema([
                        Toggle::make('auto_run')
                            ->label('Activar motor en segundo plano')
                            ->live(),
                        
                        Select::make('interval')
                            ->label('Frecuencia de análisis')
                            ->options([
                                'hourly' => 'Cada hora',
                                'everyFourHours' => 'Cada 4 horas',
                                'everySixHours' => 'Cada 6 horas',
                                'daily' => 'Una vez al día (Medianoche)',
                            ])
                            ->visible(fn (\Filament\Forms\Get $get) => $get('auto_run'))
                            ->required(fn (\Filament\Forms\Get $get) => $get('auto_run')),
                    ])
                    ->columns(2),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        Cache::forever('wids_auto_run', $data['auto_run']);
        Cache::forever('wids_interval', $data['interval'] ?? 'daily');

        Notification::make()
            ->title('Configuración guardada con éxito')
            ->success()
            ->send();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('runAnomalies')
                ->label('Ejecutar Detección Ahora')
                ->icon('heroicon-o-play')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('¿Iniciar análisis manual?')
                ->action(function () {
                    $this->executeAnomalyScript();
                }),
        ];
    }

    private function executeAnomalyScript(): void
    {
        try {
            $exitCode = Artisan::call(DetectBehaviorAnomalies::class);
            if ($exitCode === 0) {
                Notification::make()->title('Análisis completado')->success()->send();
            } else {
                Notification::make()->title('Error en el Motor')->danger()->send();
            }
        } catch (\Exception $e) {
            Notification::make()->title('Error crítico')->body($e->getMessage())->danger()->send();
        }
    }
}