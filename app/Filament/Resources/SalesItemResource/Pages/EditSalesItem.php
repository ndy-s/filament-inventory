<?php

namespace App\Filament\Resources\SalesItemResource\Pages;

use App\Filament\Resources\SalesItemResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSalesItem extends EditRecord
{
    protected static string $resource = SalesItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
