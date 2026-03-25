<?php

namespace App\Filament\Resources\LearningPhrases\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class LearningPhraseForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('episode_title')
                    ->label('Tytuł odcinka')
                    ->maxLength(255),
                Select::make('english_level')
                    ->label('Poziom angielskiego')
                    ->options([
                        'A1' => 'A1',
                        'A2' => 'A2',
                        'B1' => 'B1',
                        'B2' => 'B2',
                        'C1' => 'C1',
                        'C2' => 'C2',
                    ])
                    ->required(),
                TextInput::make('phrase')
                    ->label('Fraza')
                    ->required()
                    ->maxLength(255),
                TextInput::make('translation')
                    ->label('Tłumaczenie')
                    ->required()
                    ->maxLength(255),
                Textarea::make('context_sentence')
                    ->label('Zdanie kontekstowe')
                    ->rows(3)
                    ->columnSpanFull(),
                Textarea::make('explanation')
                    ->label('Wyjaśnienie')
                    ->rows(3)
                    ->columnSpanFull(),
            ]);
    }
}
