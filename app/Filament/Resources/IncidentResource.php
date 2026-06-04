<?php

namespace App\Filament\Resources;

use App\Filament\Resources\IncidentResource\Pages;
use App\Models\Incident;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Grid as InfolistGrid;

class IncidentResource extends Resource
{
    protected static ?string $model = Incident::class;
    protected static ?string $navigationIcon = 'heroicon-o-shield-exclamation';
    protected static ?string $navigationLabel = 'Gestión de Incidentes';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()->schema([
                    Forms\Components\TextInput::make('title')->required()->disabled(),
                    Forms\Components\TextInput::make('entity_value')->label('IP / Objetivo')->disabled(),
                    Forms\Components\Select::make('status')
                        ->label('Estado del Incidente')
                        ->options([
                            'open' => 'Abierto (En investigación)',
                            'mitigated' => 'Mitigado (Solucionado)',
                            'false_positive' => 'Falso Positivo',
                        ])->required(),
                    Forms\Components\Select::make('severity')
                        ->label('Severidad Global')
                        ->options([
                            'low' => 'Baja',
                            'medium' => 'Media',
                            'high' => 'Alta',
                            'critical' => 'Crítica',
                        ])->disabled(),
                ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('ID')->sortable(),
                Tables\Columns\TextColumn::make('title')->label('Incidente')->searchable(),
                Tables\Columns\TextColumn::make('entity_value')->label('IP Atacante')->searchable()->badge(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'open' => 'danger',
                        'mitigated' => 'success',
                        'false_positive' => 'gray',
                    }),
                Tables\Columns\TextColumn::make('severity')
                    ->label('Severidad')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'critical' => 'danger',
                        'high' => 'warning',
                        'medium' => 'primary',
                        'low' => 'gray',
                    }),
                Tables\Columns\TextColumn::make('last_seen')->label('Última actividad')->dateTime('d/m/Y H:i')->sortable(),
            ])
            ->defaultSort('last_seen', 'desc')
            ->actions([
                Tables\Actions\ViewAction::make(), 
                Tables\Actions\EditAction::make(),
            ]);
    }


 public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                InfolistGrid::make(2)->schema([
                    TextEntry::make('title')->label('Incidente Principal'),
                    TextEntry::make('entity_value')->label('IP Origen del Ataque'),
                ]),  

                RepeatableEntry::make('detections')
                    ->label('Timeline de Evidencias Registradas')
                    ->schema([
                        InfolistGrid::make(4)->schema([
                            TextEntry::make('created_at')
                                ->label('Fecha/Hora')
                                ->dateTime('d/m/Y H:i:s'),
                                
                            TextEntry::make('rule_name')
                                ->label('Firma / Regla')
                                ->formatStateUsing(fn(string $state)=> class_basename($state))
                                ->color('primary')
                                ->url(fn($record) => \App\Filament\Resources\DetectionResource::getUrl('edit', ['record' => $record]))
                                ->openUrlInNewTab(),
                                
                            TextEntry::make('score')
                                ->label('Score de Alerta')
                                ->badge(),
                                
                            TextEntry::make('id')
                                ->label('Acción')
                                ->default('Ver Detalles ↗')
                                ->color('primary')
                                ->url(fn($record) => \App\Filament\Resources\DetectionResource::getUrl('edit', ['record' => $record]))
                                ->openUrlInNewTab(),
                        ]),
                    ])->columns(1)
            ]);
    }
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListIncidents::route('/'),
            'create' => Pages\CreateIncident::route('/create'),
            'view' => Pages\ViewIncident::route('/{record}'),
            'edit' => Pages\EditIncident::route('/{record}/edit'),
        ];
    }
}