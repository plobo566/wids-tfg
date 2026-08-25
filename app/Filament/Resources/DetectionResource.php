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

    protected static ?string $navigationIcon = 'heroicon-o-bell-alert';

    protected static ?string $navigationLabel = 'Detecciones';
    protected static ?string $pluralModelLabel = 'Detecciones';
    protected static ?string $modelLabel = 'Detección';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información Forense de la Alerta')
                    ->schema([
                        Forms\Components\TextInput::make('rule_name')
                            ->label('Regla Disparada')
                            ->disabled(),

                        Forms\Components\TextInput::make('type')
                            ->label('Origen')
                            ->disabled(),

                        Forms\Components\TextInput::make('entity_value')
                            ->label('Objetivo Atacado (IP)')
                            ->disabled(),

                        Forms\Components\TextInput::make('score')
                            ->label('Severidad / Score')
                            ->disabled(),

                        Forms\Components\DateTimePicker::make('created_at')
                            ->label('Detectado el')
                            ->disabled()
                            ->timezone('Europe/Madrid'),

                        Forms\Components\Textarea::make('details')
                            ->label('Metadatos / Payload Completo')
                            ->disabled()
                            ->rows(12)
                            ->columnSpanFull()
                            ->afterStateHydrated(fn ($state, $set) => $set('details', json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE))),
                    ])->columns(2)
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
                    ->label('Objetivo / IP')
                    ->searchable()
                    ->copyable()
                    ->copyableState(fn (?string $state): ?string => $state)
                    ->description(fn($record): string => 'Tipo ' . strtoupper($record->entity_type))
                    ->html()
                    ->formatStateUsing(function (?string $state) {
                        
                        if (!$state || !filter_var($state, FILTER_VALIDATE_IP)) {
                            return $state ?? '-';
                        }

                        if (!filter_var($state, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                            return '<div class="flex items-center gap-2">
                                        <span title="Red Local" class="text-base">💻</span>
                                        <span>' . $state . '</span>
                                    </div>';
                        }

                        $flagUrl = \Illuminate\Support\Facades\Cache::remember("geoip_img_{$state}", 86400, function () use ($state) {
                            try {
                                $response = \Illuminate\Support\Facades\Http::timeout(2)->get("http://ipwho.is/{$state}");
                                
                                if ($response->successful() && $response->json('success')) {
                                    return $response->json('flag.img'); 
                                }
                            } catch (\Exception $e) {
                                return null;
                            }
                            return null;
                        });
                        
                        if ($flagUrl) {
                            return '<div class="flex items-center gap-2">
                                       <img src="' . $flagUrl . '" style="border-radius: 3px; border: 1px solid #2d3748;" class="h-3.5 w-5 object-cover shadow-sm" alt="Bandera">
                                        <span>' . $state . '</span>
                                    </div>';
                        }

                        return '<div class="flex items-center gap-2">
                                    <span title="Desconocido" class="text-base">🌐</span>
                                    <span>' . $state . '</span>
                                </div>';
                    }),

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
                    ->timezone('Europe/Madrid')
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
