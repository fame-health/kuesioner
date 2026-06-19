<?php

namespace App\Filament\Resources\Questionnaires\Pages;

use App\Exports\ResponsesExport;
use App\Filament\Resources\Questionnaires\QuestionnaireResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class EditQuestionnaire extends EditRecord
{
    protected static string $resource = QuestionnaireResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('open_public')
                ->label('Buka Link')
                ->icon(Heroicon::Link)
                ->url(fn (): string => $this->record->publicUrl())
                ->openUrlInNewTab(),
            Action::make('export')
                ->label('Export Excel')
                ->icon(Heroicon::ArrowDownTray)
                ->action(fn () => Excel::download(
                    new ResponsesExport($this->record, auth()->user()),
                    'hasil-kuisioner-'.Str::slug($this->record->title).'.xlsx',
                )),
            DeleteAction::make(),
        ];
    }
}
