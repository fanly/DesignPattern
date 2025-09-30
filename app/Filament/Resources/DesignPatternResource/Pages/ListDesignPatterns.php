<?php

namespace App\Filament\Resources\DesignPatternResource\Pages;

use App\Filament\Resources\DesignPatternResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDesignPatterns extends ListRecords
{
    protected static string $resource = DesignPatternResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
