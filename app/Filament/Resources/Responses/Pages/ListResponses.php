<?php

namespace App\Filament\Resources\Responses\Pages;

use App\Exports\ResponsesExport;
use App\Filament\Resources\Responses\ResponseResource;
use App\Models\Questionnaire;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Icons\Heroicon;
use Maatwebsite\Excel\Facades\Excel;

class ListResponses extends ListRecords
{
    protected static string $resource = ResponseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('export')
                ->label('Export Excel')
                ->icon(Heroicon::ArrowDownTray)
                ->action(function () {
                    $questionnaire = request()->query('questionnaire')
                        ? Questionnaire::query()->find(request()->query('questionnaire'))
                        : null;

                    return Excel::download(
                        new ResponsesExport($questionnaire, auth()->user()),
                        'hasil-respons.xlsx',
                    );
                }),
        ];
    }
}
