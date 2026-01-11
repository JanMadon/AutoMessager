<?php

namespace App\Filament\Resources\MailBoxes\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MailBoxesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->searchable(),
                TextColumn::make('gmailAccounts.email')->searchable(),
                TextColumn::make('subject')->searchable(),
                TextColumn::make('sent_at')
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                ViewAction::make('showBody')
                    ->label('Podgląd')
                    ->modalHeading(fn ($record) => $record->subject)
                    ->modalWidth('4xl')
                    ->modalContent(fn ($record) => view(
                        'filament.mailbox.body',
                        ['record' => $record]
                    )),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
