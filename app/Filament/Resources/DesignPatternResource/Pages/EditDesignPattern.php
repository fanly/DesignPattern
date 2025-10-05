<?php

namespace App\Filament\Resources\DesignPatternResource\Pages;

use App\Filament\Resources\DesignPatternResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDesignPattern extends EditRecord
{
    protected static string $resource = DesignPatternResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
