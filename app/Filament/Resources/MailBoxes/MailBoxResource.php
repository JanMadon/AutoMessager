<?php

namespace App\Filament\Resources\MailBoxes;

use App\Filament\Resources\MailBoxes\Pages\CreateMailBox;
use App\Filament\Resources\MailBoxes\Pages\EditMailBox;
use App\Filament\Resources\MailBoxes\Pages\ListMailBoxes;
use App\Filament\Resources\MailBoxes\Schemas\MailBoxForm;
use App\Filament\Resources\MailBoxes\Tables\MailBoxesTable;
use App\Models\MailBox;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class MailBoxResource extends Resource
{
    protected static ?string $model = MailBox::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return MailBoxForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MailBoxesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMailBoxes::route('/'),
            'create' => CreateMailBox::route('/create'),
            'edit' => EditMailBox::route('/{record}/edit'),
        ];
    }
}
