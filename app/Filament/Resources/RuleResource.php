<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RuleResource\Pages;
use App\Models\Rule;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class RuleResource extends Resource
{
    protected static ?string $model = Rule::class;

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';
    protected static ?string $navigationLabel = 'Configuración de Reglas';

    /**
     * Esta función bloquea la creación de nuevas reglas desde el panel.
     * Solo permitirá ver y editar las que ya existen en la base de datos.
     */
    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nombre de la Regla')
                    ->disabled() // Bloqueado: no se puede editar
                    ->columnSpan(1),
                    
                Forms\Components\TextInput::make('class_name')
                    ->label('Clase PHP (Backend)')
                    ->disabled() // Bloqueado: no se puede romper el enlace
                    ->columnSpan(1),
                    
                Forms\Components\TextInput::make('priority')
                    ->label('Prioridad de Ejecución')
                    ->numeric()
                    ->required()
                    ->columnSpan(1),

                Forms\Components\Toggle::make('is_enabled')
                    ->label('Regla Activada')
                    ->columnSpan(1),

                Forms\Components\KeyValue::make('settings')
                    ->label('Configuración (Umbrales y Tiempos)')
                    ->keyLabel('Parámetro')
                    ->valueLabel('Valor')
                    ->columnSpanFull(),
            ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('class_name')
                    ->label('Clase PHP')
                    ->searchable()
                    ->formatStateUsing(fn (string $state): string => class_basename($state))
                    ->color('gray'),

                Tables\Columns\TextColumn::make('priority')
                    ->label('Prioridad')
                    ->sortable(),

                Tables\Columns\ToggleColumn::make('is_enabled')
                    ->label('Estado (On/Off)'),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Última Modificación')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                // Eliminamos las acciones masivas de borrado para evitar accidentes
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRules::route('/'),
            'edit' => Pages\EditRule::route('/{record}/edit'),
        ];
    }
}