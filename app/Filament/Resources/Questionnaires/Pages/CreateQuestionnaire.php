<?php

namespace App\Filament\Resources\Questionnaires\Pages;

use App\Filament\Resources\Questionnaires\QuestionnaireResource;
use Filament\Resources\Pages\CreateRecord;

class CreateQuestionnaire extends CreateRecord
{
    protected static string $resource = QuestionnaireResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (! auth()->user()?->isAdmin()) {
            $data['user_id'] = auth()->id();
        }

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('edit', ['record' => $this->record]);
    }
}
