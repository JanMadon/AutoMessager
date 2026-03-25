<?php

namespace App\Filament\Resources\LearningPhrases;

use App\Filament\Resources\LearningPhrases\Pages\CreateLearningPhrase;
use App\Filament\Resources\LearningPhrases\Pages\EditLearningPhrase;
use App\Filament\Resources\LearningPhrases\Pages\ListLearningPhrases;
use App\Filament\Resources\LearningPhrases\Schemas\LearningPhraseForm;
use App\Filament\Resources\LearningPhrases\Tables\LearningPhrasesTable;
use App\Models\LearningPhrase;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class LearningPhraseResource extends Resource
{
    protected static ?string $model = LearningPhrase::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|\UnitEnum|null $navigationGroup = 'English Learning';

    protected static ?string $navigationLabel = 'Zapisane frazy';

    public static function form(Schema $schema): Schema
    {
        return LearningPhraseForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LearningPhrasesTable::configure($table);
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
            'index' => ListLearningPhrases::route('/'),
            'create' => CreateLearningPhrase::route('/create'),
            'edit' => EditLearningPhrase::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $userId = Auth::id();

        if ($userId === null) {
            return $query->whereRaw('1 = 0');
        }

        return $query->where('user_id', $userId);
    }
}
