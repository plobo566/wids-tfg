<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DetectionResource\Pages;
use App\Filament\Resources\DetectionResource\RelationManagers;
use App\Models\Detection;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DetectionResource extends Resource
{
    protected static ?string $model = Detection::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function canCreate(): bool{
        return false;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('type')
                    ->label('Origen')
                    ->badge()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('rule_name')
                    ->label('Regla Disparada')
                    ->searchable()
                    ->formatStateUsing(fn(string $state):string=>class_basename($state)),

                Tables\Columns\TextColumn::make('entity_value')
                    ->label('Objetivo Atacado')
                    ->searchable()
                    ->copyable()
                    ->description(fn($record): string => 'Tipo ' . strtoupper($record->entity_type)),

                Tables\Columns\TextColumn::make('score')
                    ->label('Severidad')
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->color(fn (int|string $state): string =>match(true){
                        (int) $state >= 80 => 'danger', //rojo
                        (int) $state >= 50 => 'warning', //amarillo
                        default => 'success',
                    }),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Detectado el:')
                    ->dateTime('d/m/Y H:i:s')
                    ->sortable(),



                

            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListDetections::route('/'),
            'create' => Pages\CreateDetection::route('/create'),
            'edit' => Pages\EditDetection::route('/{record}/edit'),
        ];
    }
}
