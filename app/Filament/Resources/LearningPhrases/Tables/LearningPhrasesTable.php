<?php

namespace App\Filament\Resources\LearningPhrases\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class LearningPhrasesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->sortable(),
                TextColumn::make('episode_title')
                    ->label('Odcinek')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('english_level')
                    ->label('Poziom')
                    ->badge()
                    ->sortable(),
                TextColumn::make('phrase')
                    ->label('Fraza')
                    ->searchable()
                    ->wrap()
                    ->limit(60),
                TextColumn::make('translation')
                    ->label('Tłumaczenie')
                    ->searchable()
                    ->wrap()
                    ->limit(60),
                TextColumn::make('created_at')
                    ->label('Dodano')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('english_level')
                    ->label('Poziom')
                    ->options([
                        'A1' => 'A1',
                        'A2' => 'A2',
                        'B1' => 'B1',
                        'B2' => 'B2',
                        'C1' => 'C1',
                        'C2' => 'C2',
                    ]),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
