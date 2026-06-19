<?php

namespace App\Filament\Resources\Responses;

use App\Exports\ResponsesExport;
use App\Filament\Resources\Responses\Pages\ListResponses;
use App\Filament\Resources\Responses\Pages\ViewResponse;
use App\Models\Questionnaire;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use UnitEnum;

class ResponseResource extends Resource
{
    protected static ?string $model = Questionnaire::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::ChartBar;

    protected static ?string $navigationLabel = 'Hasil Respons';

    protected static ?string $modelLabel = 'Hasil Kuisioner';

    protected static ?string $pluralModelLabel = 'Hasil Respons';

    protected static string|UnitEnum|null $navigationGroup = 'Laporan';

    public static function form(Schema $schema): Schema
    {
        return $schema;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Kuisioner')
                    ->description(fn (Questionnaire $record): ?string => $record->description ? Str::limit($record->description, 90) : null)
                    ->searchable()
                    ->sortable()
                    ->wrap(),
                TextColumn::make('user.name')
                    ->label('Pemilik')
                    ->visible(fn (): bool => auth()->user()?->isAdmin() ?? false)
                    ->searchable()
                    ->sortable(),
                TextColumn::make('questions_count')
                    ->label('Pertanyaan')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('responses_count')
                    ->label('Respons')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('is_active')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (bool $state): string => $state ? 'Aktif' : 'Nonaktif')
                    ->color(fn (bool $state): string => $state ? 'success' : 'gray')
                    ->sortable(),
                TextColumn::make('expired_at')
                    ->label('Expired')
                    ->dateTime('d M Y H:i')
                    ->placeholder('Tanpa batas')
                    ->sortable(),
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->label('Status aktif'),
            ])
            ->recordActions([
                ViewAction::make()
                    ->label('Lihat Analisis'),
                Action::make('export')
                    ->label('Export Excel')
                    ->icon(Heroicon::ArrowDownTray)
                    ->action(fn (Questionnaire $record) => Excel::download(
                        new ResponsesExport($record, auth()->user()),
                        'hasil-kuisioner-'.Str::slug($record->title).'.xlsx',
                    )),
            ]);
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()
            ->with('user')
            ->withCount(['questions', 'responses']);

        if (! auth()->user()?->isAdmin()) {
            $query->where('user_id', auth()->id());
        }

        return $query;
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
            'index' => ListResponses::route('/'),
            'view' => ViewResponse::route('/{record}'),
        ];
    }
}
