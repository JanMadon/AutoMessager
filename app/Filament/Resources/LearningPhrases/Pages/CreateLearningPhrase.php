<?php

namespace App\Filament\Resources\LearningPhrases\Pages;

use App\Filament\Resources\LearningPhrases\LearningPhraseResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateLearningPhrase extends CreateRecord
{
    protected static string $resource = LearningPhraseResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = Auth::id();

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
