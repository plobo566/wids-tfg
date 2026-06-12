<?php

namespace App\Filament\Resources\AlertSettingResource\Pages;

use App\Filament\Resources\AlertSettingResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAlertSettings extends ListRecords
{
    protected static string $resource = AlertSettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
