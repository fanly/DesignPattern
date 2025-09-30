<?php

namespace App\Filament\Resources\PatternCategoryResource\Pages;

use App\Filament\Resources\PatternCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPatternCategory extends EditRecord
{
    protected static string $resource = PatternCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
