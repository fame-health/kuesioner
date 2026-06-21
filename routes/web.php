<?php

use App\Http\Controllers\PublicQuestionnaireController;
use App\Models\Questionnaire;
use App\Services\ChoiceAnalysisService;
use Illuminate\Support\Facades\Route;

Route::get('/', function (ChoiceAnalysisService $choiceAnalysisService) {
    $questionnaires = Questionnaire::query()
        ->available()
        ->with('user:id,name')
        ->with([
            'questions' => fn ($query) => $query
                ->where('show_public_analysis', true)
                ->with('options'),
        ])
        ->withCount(['questions', 'responses'])
        ->latest()
        ->get();

    return view('welcome', [
        'questionnaires' => $questionnaires,
        'publicAnalyses' => $questionnaires
            ->map(fn (Questionnaire $questionnaire): array => [
                'questionnaire' => $questionnaire,
                'questions' => $choiceAnalysisService->forQuestionnaire($questionnaire, publicOnly: true),
            ])
            ->filter(fn (array $analysis): bool => $analysis['questions'] !== [])
            ->values(),
    ]);
});

Route::get('/q/{token}', [PublicQuestionnaireController::class, 'show'])
    ->name('questionnaires.public.show');
Route::post('/q/{token}', [PublicQuestionnaireController::class, 'submit'])
    ->name('questionnaires.public.submit');
Route::get('/q/{token}/success', [PublicQuestionnaireController::class, 'success'])
    ->name('questionnaires.public.success');
