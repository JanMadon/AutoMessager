<?php

namespace App\Filament\Resources\Notes\Schemas;

use App\Enums\NoteTagEnums;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class NoteForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->label('Tytuł')
                    ->required()
                    ->unique(),
                Select::make('tag')
                    ->options(NoteTagEnums::class),
                MarkdownEditor::make('content')
                    ->label('Kontent')
                    ->columnSpanFull()
                    ->required(),
            ]);
    }
}
