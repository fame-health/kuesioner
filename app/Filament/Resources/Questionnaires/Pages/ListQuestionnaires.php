<?php

namespace App\Filament\Resources\Questionnaires\Pages;

use App\Exports\ResponsesExport;
use App\Filament\Resources\Questionnaires\QuestionnaireResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Icons\Heroicon;
use Maatwebsite\Excel\Facades\Excel;

class ListQuestionnaires extends ListRecords
{
    protected static string $resource = QuestionnaireResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('export_all')
                ->label('Export Semua Hasil')
                ->icon(Heroicon::ArrowDownTray)
                ->action(fn () => Excel::download(
                    new ResponsesExport(user: auth()->user()),
                    'hasil-semua-kuisioner.xlsx',
                )),
            CreateAction::make(),
        ];
    }
}
