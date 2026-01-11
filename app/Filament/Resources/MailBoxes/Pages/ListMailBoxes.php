<?php

namespace App\Filament\Resources\MailBoxes\Pages;

use App\Filament\Resources\MailBoxes\MailBoxResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListMailBoxes extends ListRecords
{
    protected static string $resource = MailBoxResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
