<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AlertSettingResource\Pages;
use App\Models\AlertSetting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AlertSettingResource extends Resource
{
    protected static ?string $model = AlertSetting::class;

    protected static ?string $navigationGroup = 'Sistema';
    
    protected static ?string $navigationIcon = 'heroicon-o-envelope-open';
    
    //nombres en español
    protected static ?string $navigationLabel = 'Configuración de Alertas';
    protected static ?string $modelLabel = 'Configuración de Alerta';
    protected static ?string $pluralModelLabel = 'Configuración de Alertas';

    //solo permite crear un registro si la tabla está vacía
    public static function canCreate(): bool
    {
        return AlertSetting::count() === 0;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Ajustes del Webhook / Correo')
                    ->description('Configura dónde y cuándo se enviarán las notificaciones de ataques.')
                    ->schema([
                        Forms\Components\TextInput::make('email_destination')
                            ->label('Correo de Destino')
                            ->email()
                            ->required()
                            ->placeholder('admin@tu-universidad.es'),
                            
                        Forms\Components\TextInput::make('threshold')
                            ->label('Baremo Mínimo para Alerta (Threshold)')
                            ->numeric()
                            ->required()
                            ->helperText('Solo se enviará un correo si el nivel de amenaza supera este número.')
                            ->minValue(1),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('email_destination')
                    ->label('Correo de Destino')
                    ->icon('heroicon-m-envelope'),
                Tables\Columns\TextColumn::make('threshold')
                    ->label('Baremo')
                    ->badge()
                    ->color('warning'),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Última modificación')
                    ->dateTime('d/m/Y H:i'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAlertSettings::route('/'),
            'create' => Pages\CreateAlertSetting::route('/create'),
            'edit' => Pages\EditAlertSetting::route('/{record}/edit'),
        ];
    }
}