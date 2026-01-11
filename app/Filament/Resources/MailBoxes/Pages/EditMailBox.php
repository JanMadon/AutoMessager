<?php

namespace App\Filament\Resources\MailBoxes\Pages;

use App\Filament\Resources\MailBoxes\MailBoxResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditMailBox extends EditRecord
{
    protected static string $resource = MailBoxResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
