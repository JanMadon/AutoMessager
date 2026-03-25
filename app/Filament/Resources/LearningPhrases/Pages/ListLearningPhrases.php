<?php

namespace App\Filament\Resources\LearningPhrases\Pages;

use App\Filament\Resources\LearningPhrases\LearningPhraseResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListLearningPhrases extends ListRecords
{
    protected static string $resource = LearningPhraseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
