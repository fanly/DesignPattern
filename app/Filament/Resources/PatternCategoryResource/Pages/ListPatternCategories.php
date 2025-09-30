<?php

namespace App\Filament\Resources\PatternCategoryResource\Pages;

use App\Filament\Resources\PatternCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPatternCategories extends ListRecords
{
    protected static string $resource = PatternCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
