<?php

namespace App\Filament\Resources\Questionnaires;

use App\Exports\ResponsesExport;
use App\Filament\Resources\Questionnaires\Pages\CreateQuestionnaire;
use App\Filament\Resources\Questionnaires\Pages\EditQuestionnaire;
use App\Filament\Resources\Questionnaires\Pages\ListQuestionnaires;
use App\Filament\Resources\Questionnaires\RelationManagers\QuestionsRelationManager;
use App\Filament\Resources\Responses\ResponseResource;
use App\Models\Questionnaire;
use App\Models\User;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use UnitEnum;

class QuestionnaireResource extends Resource
{
    protected static ?string $model = Questionnaire::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::ClipboardDocumentList;

    protected static ?string $navigationLabel = 'Kuisioner';

    protected static ?string $modelLabel = 'Kuisioner';

    protected static ?string $pluralModelLabel = 'Kuisioner';

    protected static string|UnitEnum|null $navigationGroup = 'Kuisioner';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make([
                    'default' => 1,
                    'lg' => 3,
                ])->schema([
                    Section::make('Informasi Kuisioner')
                        ->compact()
                        ->schema([
                            TextInput::make('title')
                                ->label('Judul')
                                ->required()
                                ->maxLength(255),
                            Select::make('user_id')
                                ->label('Pemilik')
                                ->options(fn () => User::query()->where('is_active', true)->orderBy('name')->pluck('name', 'id'))
                                ->searchable()
                                ->required()
                                ->default(fn () => auth()->id())
                                ->disabled(fn () => ! auth()->user()?->isAdmin())
                                ->dehydrated(),
                            Textarea::make('description')
                                ->label('Deskripsi')
                                ->rows(3)
                                ->columnSpanFull(),
                        ])
                        ->columns([
                            'default' => 1,
                            'md' => 2,
                        ])
                        ->columnSpan([
                            'default' => 'full',
                            'lg' => 2,
                        ]),
                    Section::make('Publikasi')
                        ->compact()
                        ->schema([
                            Toggle::make('is_active')
                                ->label('Aktif')
                                ->default(false),
                            DateTimePicker::make('expired_at')
                                ->label('Batas waktu pengisian')
                                ->seconds(false)
                                ->native(false),
                            TextInput::make('public_token')
                                ->label('Token publik')
                                ->disabled()
                                ->dehydrated()
                                ->copyable(),
                        ])
                        ->columnSpan([
                            'default' => 'full',
                            'lg' => 1,
                        ]),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('Judul')
                    ->description(fn (Questionnaire $record): ?string => $record->description ? Str::limit($record->description, 70) : null)
                    ->searchable()
                    ->sortable(),
                TextColumn::make('user.name')
                    ->label('Pemilik')
                    ->visible(fn () => auth()->user()?->isAdmin())
                    ->searchable()
                    ->sortable(),
                TextColumn::make('public_token')
                    ->label('Link')
                    ->state(fn (Questionnaire $record): string => $record->publicUrl())
                    ->copyable()
                    ->copyMessage('Link kuisioner disalin')
                    ->limit(36),
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
                TextColumn::make('questions_count')
                    ->label('Pertanyaan')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('responses_count')
                    ->label('Respons')
                    ->numeric()
                    ->sortable(),
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->label('Status aktif'),
                SelectFilter::make('user_id')
                    ->label('Pemilik')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->visible(fn () => auth()->user()?->isAdmin()),
            ])
            ->recordActions([
                Action::make('toggle_active')
                    ->label(fn (Questionnaire $record): string => $record->is_active ? 'Nonaktifkan' : 'Aktifkan')
                    ->icon(fn (Questionnaire $record): Heroicon => $record->is_active ? Heroicon::BoltSlash : Heroicon::Bolt)
                    ->color(fn (Questionnaire $record): string => $record->is_active ? 'gray' : 'success')
                    ->requiresConfirmation()
                    ->action(fn (Questionnaire $record): bool => $record->update(['is_active' => ! $record->is_active])),
                Action::make('open_public')
                    ->label('Buka Link')
                    ->icon(Heroicon::Link)
                    ->url(fn (Questionnaire $record): string => $record->publicUrl())
                    ->openUrlInNewTab(),
                Action::make('view_results')
                    ->label('Lihat Hasil')
                    ->icon(Heroicon::ChartBar)
                    ->url(fn (Questionnaire $record): string => ResponseResource::getUrl('view', ['record' => $record])),
                Action::make('export')
                    ->label('Export Excel')
                    ->icon(Heroicon::ArrowDownTray)
                    ->action(fn (Questionnaire $record) => Excel::download(
                        new ResponsesExport($record, auth()->user()),
                        'hasil-kuisioner-'.Str::slug($record->title).'.xlsx',
                    )),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()
            ->with('user')
            ->withCount(['questions', 'responses']);

        if (auth()->user()?->isAdmin()) {
            return $query;
        }

        return $query->where('user_id', auth()->id());
    }

    public static function getRelations(): array
    {
        return [
            QuestionsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListQuestionnaires::route('/'),
            'create' => CreateQuestionnaire::route('/create'),
            'edit' => EditQuestionnaire::route('/{record}/edit'),
        ];
    }
}
