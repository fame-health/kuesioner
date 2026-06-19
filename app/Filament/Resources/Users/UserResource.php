<?php

namespace App\Filament\Resources\Users;

use App\Filament\Resources\Users\Pages\CreateUser;
use App\Filament\Resources\Users\Pages\EditUser;
use App\Filament\Resources\Users\Pages\ListUsers;
use App\Models\User;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;
use UnitEnum;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Users;

    protected static ?string $navigationLabel = 'User';

    protected static ?string $modelLabel = 'User';

    protected static ?string $pluralModelLabel = 'User';

    protected static string|UnitEnum|null $navigationGroup = 'Administrasi';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nama')
                    ->required()
                    ->maxLength(255),
                TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                TextInput::make('password')
                    ->label('Password')
                    ->password()
                    ->revealable()
                    ->required(fn (string $operation): bool => $operation === 'create')
                    ->dehydrated(fn (?string $state): bool => filled($state))
                    ->maxLength(255),
                Select::make('role')
                    ->label('Role')
                    ->options([
                        'admin' => 'Admin',
                        'user' => 'User',
                    ])
                    ->required()
                    ->default('user'),
                Toggle::make('is_active')
                    ->label('Aktif')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('role')
                    ->label('Role')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => $state === 'admin' ? 'Admin' : 'User')
                    ->color(fn (string $state): string => $state === 'admin' ? 'danger' : 'info')
                    ->sortable(),
                TextColumn::make('is_active')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (bool $state): string => $state ? 'Aktif' : 'Nonaktif')
                    ->color(fn (bool $state): string => $state ? 'success' : 'gray')
                    ->sortable(),
                TextColumn::make('questionnaires_count')
                    ->label('Kuisioner')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('role')
                    ->label('Role')
                    ->options([
                        'admin' => 'Admin',
                        'user' => 'User',
                    ]),
                TernaryFilter::make('is_active')
                    ->label('Status aktif'),
            ])
            ->recordActions([
                Action::make('reset_password')
                    ->label('Reset Password')
                    ->icon(Heroicon::Key)
                    ->form([
                        TextInput::make('password')
                            ->label('Password baru')
                            ->password()
                            ->revealable()
                            ->required()
                            ->minLength(8),
                    ])
                    ->action(function (User $record, array $data): void {
                        $record->forceFill([
                            'password' => Hash::make($data['password']),
                        ])->save();

                        Notification::make()
                            ->title('Password user berhasil direset')
                            ->success()
                            ->send();
                    }),
                EditAction::make(),
                DeleteAction::make()
                    ->visible(fn (User $record): bool => $record->id !== auth()->id()),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withCount('questionnaires');
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
            'index' => ListUsers::route('/'),
            'create' => CreateUser::route('/create'),
            'edit' => EditUser::route('/{record}/edit'),
        ];
    }
}
