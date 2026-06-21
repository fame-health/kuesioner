<?php

namespace App\Services;

use App\Models\Answer;
use App\Models\Question;
use App\Models\Questionnaire;
use Illuminate\Support\Collection;

class ChoiceAnalysisService
{
    public function forQuestionnaire(Questionnaire $questionnaire, bool $publicOnly = false): array
    {
        $questionnaire->loadMissing('questions.options');

        $questions = $questionnaire->questions
            ->filter(fn (Question $question): bool => $question->usesOptions())
            ->when(
                $publicOnly,
                fn (Collection $questions): Collection => $questions
                    ->where('show_public_analysis', true),
            )
            ->values();

        if ($questions->isEmpty()) {
            return [];
        }

        $answersByQuestion = Answer::query()
            ->whereIn('question_id', $questions->modelKeys())
            ->whereHas(
                'response',
                fn ($query) => $query->where('questionnaire_id', $questionnaire->getKey()),
            )
            ->get(['question_id', 'answer_text', 'answer_json'])
            ->groupBy('question_id');

        return $questions
            ->map(function (Question $question) use ($answersByQuestion): array {
                $counts = $question->options
                    ->pluck('option_text')
                    ->mapWithKeys(fn (string $option): array => [$option => 0])
                    ->all();

                $answered = 0;

                foreach ($answersByQuestion->get($question->getKey(), collect()) as $answer) {
                    $values = $answer->answer_json !== null
                        ? $answer->answer_json
                        : [$answer->answer_text];
                    $values = array_values(array_filter(
                        $values,
                        fn (mixed $value): bool => filled($value),
                    ));

                    if ($values === []) {
                        continue;
                    }

                    $answered++;

                    foreach ($values as $value) {
                        $counts[$value] = ($counts[$value] ?? 0) + 1;
                    }
                }

                return [
                    'question' => $question->question_text,
                    'type' => Question::TYPES[$question->question_type] ?? $question->question_type,
                    'is_multiple' => $question->question_type === Question::TYPE_CHECKBOX,
                    'answered' => $answered,
                    'options' => collect($counts)
                        ->map(fn (int $count, string $option): array => [
                            'label' => $option,
                            'count' => $count,
                            'percentage' => $answered > 0
                                ? round(($count / $answered) * 100, 1)
                                : 0,
                        ])
                        ->values()
                        ->all(),
                ];
            })
            ->all();
    }
}
