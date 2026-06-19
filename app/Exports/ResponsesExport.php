<?php

namespace App\Exports;

use App\Models\Questionnaire;
use App\Models\Response as QuestionnaireResponse;
use App\Models\User;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ResponsesExport implements FromCollection, WithHeadings
{
    public function __construct(
        private ?Questionnaire $questionnaire = null,
        private ?User $user = null,
    ) {}

    public function headings(): array
    {
        return [
            'Nama Kuisioner',
            'Nama Responden',
            'Email Responden',
            'Tanggal Submit',
            'Pertanyaan',
            'Jawaban',
        ];
    }

    public function collection(): Collection
    {
        $query = QuestionnaireResponse::query()
            ->with(['questionnaire', 'answers.question'])
            ->latest('submitted_at');

        if ($this->questionnaire !== null) {
            $query->where('questionnaire_id', $this->questionnaire->id);
        }

        if ($this->user !== null && ! $this->user->isAdmin()) {
            $query->whereHas('questionnaire', function ($query): void {
                $query->where('user_id', $this->user?->id);
            });
        }

        return $query->get()->flatMap(function (QuestionnaireResponse $response): array {
            return $response->answers->map(function ($answer) use ($response): array {
                $value = $answer->answer_json !== null
                    ? implode(', ', $answer->answer_json)
                    : (string) $answer->answer_text;

                return [
                    $response->questionnaire?->title,
                    $response->respondent_name,
                    $response->respondent_email,
                    optional($response->submitted_at)->format('Y-m-d H:i:s'),
                    $answer->question?->question_text,
                    $value,
                ];
            })->all();
        });
    }
}
