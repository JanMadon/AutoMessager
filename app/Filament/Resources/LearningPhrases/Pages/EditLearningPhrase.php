<?php

namespace App\Filament\Resources\LearningPhrases\Pages;

use App\Filament\Resources\LearningPhrases\LearningPhraseResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditLearningPhrase extends EditRecord
{
    protected static string $resource = LearningPhraseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
