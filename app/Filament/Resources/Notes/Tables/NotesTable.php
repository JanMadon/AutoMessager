<?php

namespace App\Filament\Resources\Notes\Tables;

use App\Enums\NoteTagEnums;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class NotesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->sortable(),
                TextColumn::make('title'),
                TextColumn::make('tag')
                    ->sortable()
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'personal' => 'info',
                        'work' => 'warning',
                        'knowledge' => 'success',
                        'finance' => 'danger',
                        'other' => 'gray',
                        default => 'gray',
                    }),
                TextColumn::make('content')
                    ->wrap()
                    ->lineClamp(2)
                    ->limit(50)
                    ->tooltip(fn ($record): string => $record->content),
                TextColumn::make('created_at')->sortable(),
            ])
            ->filters([
                SelectFilter::make('tag')
                    ->options(NoteTagEnums::class)
                    ->label('Tag'),
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
